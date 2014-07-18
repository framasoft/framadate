<?php
require('fpdf.php');

$red = array(255,0,0);
$green = array(0,255,0);
$blue = array(0,0,255);
$black = array(0,0,0);
$formatA4 = array(595.28,841.89);

function isInteger($val)
{
	if ($val - round($val) == 0) return true;
	else return false;
}
function plus10pourcentArrondi($valeur)
{
	if ($valeur > 10000) 		$ratio=1000;
	else if ($valeur > 1000) 	$ratio=100;
	else if ($valeur > 100) 	$ratio=10;
	else 				$ratio=1;

	$res = $valeur + (0.1*$valeur);
	$res = round($res/$ratio) * $ratio;

	return $res;
}

function moins10pourcentArrondi($valeur)
{
	if ($valeur > 10000) 		$ratio=1000;
	else if ($valeur > 1000) 	$ratio=100;
	else if ($valeur > 100) 	$ratio=10;
	else 				$ratio=1;

	if ($valeur >0)
		$res = $valeur - (0.1*$valeur);
	else $res = $valeur + (0.1*$valeur);
	
	$res = round($res/$ratio) * $ratio;
	return $res;
}

class phpToPDF extends FPDF
{
	var $legends;
	var $wLegend;
	var $sum;
	var $NbVal;

	var $_toc=array();
	var $_numbering=false;
	var $_numberingFooter=false;
	var $_numPageNum=1;

	var $tb_columns; 		//number of columns of the table
	var $tb_header_type; 	//array which contains the header characteristics and texts
	var $tb_header_draw;	//TRUE or FALSE, the header is drawed or not
	var $tb_border_draw;	//TRUE or FALSE, the table border is drawed or not
	var $tb_data_type; 		//array which contains the data characteristics (only the characteristics)
	var $tb_table_type; 	//array which contains the table charactersitics
	var $table_startx, $table_starty;	//the X and Y position where the table starts

	var $Draw_Header_Command;	//command which determines in the DrawData first the header draw
	var $New_Page_Commit;	// = true/false if a new page has been comited
	var $Data_On_Current_Page; // = true/false ... if on current page was some data written	
	

	function AddPage($orientation='') {
		parent::AddPage($orientation);
		if($this->_numbering)
			$this->_numPageNum++;
	}

	function startPageNums() {
		$this->_numbering=true;
		$this->_numberingFooter=true;
	}

	function stopPageNums() {
		$this->_numbering=false;
	}

	function numPageNo() {
		return $this->_numPageNum;
	}

	function TOC_Entry($txt,$level=0) {
		$this->_toc[]=array('t'=>$txt,'l'=>$level,'p'=>$this->numPageNo());
	}

	function insertTOC( $location=1,
						$labelSize=20,
						$entrySize=10,
						$tocfont='Times',
						$label='Table des matières'
						) {
		//make toc at end
		$this->stopPageNums();
		$this->AddPage();
		$tocstart=$this->page;

		$this->SetFont($tocfont,'B',$labelSize);
		$this->Cell(0,5,$label,0,1,'C');
		$this->Ln(20);


		$this->SetLeftMargin(20);



		foreach($this->_toc as $t) {

			//Offset
			$level=$t['l'];
			if($level>0)
				$this->Cell($level*8);
			$weight='';
			if($level==0)
				$weight='B';
			$str=$t['t'];
			$this->SetFont($tocfont,$weight,$entrySize);
			$strsize=$this->GetStringWidth($str);
			$this->Cell($strsize+2,$this->FontSize+2,$str);

			//Filling dots
			$this->SetFont($tocfont,'',$entrySize);
			$PageCellSize=$this->GetStringWidth($t['p'])+2;
			$w=$this->w-$this->lMargin-$this->rMargin-$PageCellSize-($level*8)-($strsize+2);
			$nb=$w/$this->GetStringWidth('.');
			$dots=str_repeat('.',$nb);
			$this->Cell($w,$this->FontSize+2,$dots,0,0,'R');

			//Page number
			$this->Cell($PageCellSize,$this->FontSize+2,$t['p'],0,1,'R');

			$this->Ln(2);
		}

		//grab it and move to selected location
		$n=$this->page;
		$n_toc = $n - $tocstart + 1;
		$last = array();

		//store toc pages
		for($i = $tocstart;$i <= $n;$i++)
			$last[]=$this->pages[$i];

		//move pages
		for($i=$tocstart - 1;$i>=$location-1;$i--)
			$this->pages[$i+$n_toc]=$this->pages[$i];

		//Put toc pages at insert point
		for($i = 0;$i < $n_toc;$i++)
			$this->pages[$location + $i]=$last[$i];
	}

	function Footer() {
		if($this->_numberingFooter==false)
			return;
		//Go to 1.5 cm from bottom
		$this->SetY(-15);
		//Select Arial italic 8
		$this->SetFont('Arial','I',8);
		$this->Cell(0,7,$this->numPageNo(),0,0,'C'); 
		if($this->_numbering==false)
			$this->_numberingFooter=false;
	}

	function SetDash($black=false,$white=false)
	{
		if($black and $white)
			$s=sprintf('[%.3f %.3f] 0 d',$black*$this->k,$white*$this->k);
		else
			$s='[] 0 d';
		$this->_out($s);
	}
	
	function SetLegends($data, $format)
	{
		$this->legends=array();
		$this->wLegend=0;
		$this->sum=array_sum($data);
		$this->NbVal=count($data);
		foreach($data as $l=>$val)
		{
			$p=sprintf('%.2f',$val/$this->sum*100).'%';
			$legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
			$this->legends[]=$legend;
			$this->wLegend=max($this->GetStringWidth($legend),$this->wLegend);
		}
	}

	function DiagCirculaire($largeur, $hauteur, $data, $format, $couleurs=null, $legend=1)
	{
		$this->SetFont('Courier', '', 10);
		$this->SetLegends($data,$format);

		$XPage = $this->GetX();
		$YPage = $this->GetY();
		$marge = 2;
		$hLegende = 5;
		$rayon = min($largeur - $marge * 4 - $hLegende - $this->wLegend, $hauteur - $marge * 2);
		$rayon = floor($rayon / 2);
		$XDiag = $XPage + $marge + $rayon;
		$YDiag = $YPage + $marge + $rayon;
		if($couleurs == null) {
			for($i = 0;$i < $this->NbVal; $i++) {
				$gray = $i * intval(255 / $this->NbVal);
				$couleurs[$i] = array($gray,$gray,$gray);
			}
		}

		//Secteurs
		$this->SetLineWidth(0.2);
		$angleDebut = 0;
		$angleFin = 0;
		$i = 0;
		foreach($data as $val) {
			$angle = floor(($val * 360) / doubleval($this->sum));
			if ($angle != 0) {
				$angleFin = $angleDebut + $angle;
				$this->SetFillColor($couleurs[$i][0],$couleurs[$i][1],$couleurs[$i][2]);
				$this->Sector($XDiag, $YDiag, $rayon, $angleDebut, $angleFin);
				$angleDebut += $angle;
			}
			$i++;
		}
		if ($angleFin != 360) {
			$this->Sector($XDiag, $YDiag, $rayon, $angleDebut - $angle, 360);
		}

		//Légendes
		if ($legend == 1)
		{
			$this->SetFont('Courier', '', 10);
			$x1 = $XPage + 2 * $rayon + 4 * $marge;
			$x2 = $x1 + $hLegende + $marge;
			$y1 = $YDiag - $rayon + (2 * $rayon - $this->NbVal*($hLegende + $marge)) / 2;
			for($i=0; $i<$this->NbVal; $i++) {
				$this->SetFillColor($couleurs[$i][0],$couleurs[$i][1],$couleurs[$i][2]);
				$this->Rect($x1, $y1, $hLegende, $hLegende, 'DF');
				$this->SetXY($x2,$y1);
				$this->Cell(0,$hLegende,$this->legends[$i]);
				$y1+=$hLegende + $marge;
			}
		}
	}


	function DiagBatons($largeur, $hauteur, $data, $format, $couleur=null, $maxValRepere=0, $nbIndRepere=4)
	{
		$this->SetFont('Courier', '', 10);
		$this->SetLegends($data,$format);

		$XPage = $this->GetX();
		$YPage = $this->GetY();
		$marge = 2;
		$YDiag = $YPage + $marge;
		$hDiag = floor($hauteur - $marge * 2);
		$XDiag = $XPage + $marge * 2 + $this->wLegend;
		$lDiag = floor($largeur - $marge * 3 - $this->wLegend);
		if($couleur == null)
			$couleur=array(155,155,155);
		if ($maxValRepere == 0) {
			$maxValRepere = max($data);
		}
		$valIndRepere = ceil($maxValRepere / $nbIndRepere);
		$maxValRepere = $valIndRepere * $nbIndRepere;
		$lRepere = floor($lDiag / $nbIndRepere);
		$lDiag = $lRepere * $nbIndRepere;
		$unite = $lDiag / $maxValRepere;
		$hBaton = floor($hDiag / ($this->NbVal + 1));
		$hDiag = $hBaton * ($this->NbVal + 1);
		$eBaton = floor($hBaton * 80 / 100);

		$this->SetLineWidth(0.2);
		$this->Rect($XDiag, $YDiag, $lDiag, $hDiag);

		$this->SetFont('Courier', '', 10);
		$this->SetFillColor($couleur[0],$couleur[1],$couleur[2]);
		$i=0;
		foreach($data as $val) {
			//Barre
			$xval = $XDiag;
			$lval = (int)($val * $unite);
			$yval = $YDiag + ($i + 1) * $hBaton - $eBaton / 2;
			$hval = $eBaton;
			$this->Rect($xval, $yval, $lval, $hval, 'DF');
			//Légende
			$this->SetXY(0, $yval);
			$this->Cell($xval - $marge, $hval, $this->legends[$i],0,0,'R');
			$i++;
		}

		//Echelles
		for ($i = 0; $i <= $nbIndRepere; $i++) {
			$xpos = $XDiag + $lRepere * $i;
			$this->Line($xpos, $YDiag, $xpos, $YDiag + $hDiag);
			$val = $i * $valIndRepere;
			$xpos = $XDiag + $lRepere * $i - $this->GetStringWidth($val) / 2;
			$ypos = $YDiag + $hDiag - $marge;
			$this->Text($xpos, $ypos, $val);
		}
	}	

	function Sector($xc, $yc, $r, $a, $b, $style='FD', $cw=true, $o=90)
	{
		if($cw){
			$d = $b;
			$b = $o - $a;
			$a = $o - $d;
		}else{
			$b += $o;
			$a += $o;
		}
		$a = ($a%360)+360;
		$b = ($b%360)+360;
		if ($a > $b)
			$b +=360;
		$b = $b/360*2*M_PI;
		$a = $a/360*2*M_PI;
		$d = $b-$a;
		if ($d == 0 )
			$d =2*M_PI;
		$k = $this->k;
		$hp = $this->h;
		if($style=='F')
			$op='f';
		elseif($style=='FD' or $style=='DF')
			$op='b';
		else
			$op='s';
		if (sin($d/2))
			$MyArc = 4/3*(1-cos($d/2))/sin($d/2)*$r;
		//first put the center
		$this->_out(sprintf('%.2f %.2f m',($xc)*$k,($hp-$yc)*$k));
		//put the first point
		$this->_out(sprintf('%.2f %.2f l',($xc+$r*cos($a))*$k,(($hp-($yc-$r*sin($a)))*$k)));
		//draw the arc
		if ($d < M_PI/2){
			$this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
						$yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
						$xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
						$yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
						$xc+$r*cos($b),
						$yc-$r*sin($b)
						);
		}else{
			$b = $a + $d/4;
			$MyArc = 4/3*(1-cos($d/8))/sin($d/8)*$r;
			$this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
						$yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
						$xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
						$yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
						$xc+$r*cos($b),
						$yc-$r*sin($b)
						);
			$a = $b;
			$b = $a + $d/4;
			$this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
						$yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
						$xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
						$yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
						$xc+$r*cos($b),
						$yc-$r*sin($b)
						);
			$a = $b;
			$b = $a + $d/4;
			$this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
						$yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
						$xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
						$yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
						$xc+$r*cos($b),
						$yc-$r*sin($b)
						);
			$a = $b;
			$b = $a + $d/4;
			$this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
						$yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
						$xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
						$yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
						$xc+$r*cos($b),
						$yc-$r*sin($b)
						);
		}
		//terminate drawing
		$this->_out($op);
	}

	function _Arc($x1, $y1, $x2, $y2, $x3, $y3 )
	{
		$h = $this->h;
		$this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c',
			$x1*$this->k,
			($h-$y1)*$this->k,
			$x2*$this->k,
			($h-$y2)*$this->k,
			$x3*$this->k,
			($h-$y3)*$this->k));
	}	
	
	//returns the width of the page in user units
	function PageWidth(){
		return (int) $this->w-$this->rMargin-$this->lMargin;
	}

	//constructor(not a real one, but have to call it first)
	//we initialize all the variables that we use
	function Table_Init($col_no = 0, $header_draw = true, $border_draw = true){
		$this->tb_columns = $col_no;
		$this->tb_header_type = Array();
		$this->tb_header_draw = $header_draw;
		$this->tb_border_draw = $border_draw;
		$this->tb_data_type = Array();
		$this->tb_type = Array();
		$this->table_startx = $this->GetX();
		$this->table_starty = $this->GetY();

		$this->Draw_Header_Command = false; //by default we don't draw the header
		$this->New_Page_Commit = false;		//NO we do not consider first time a new page
		$this->Data_On_Current_Page = false;
	}

	//Sets the number of columns of the table
	function Set_Table_Columns($nr){
		$this->tb_columns = $nr;
	}

	/*
	Characteristics constants for Header Type:
	EVERY CELL FROM THE TABLE IS A MULTICELL

		WIDTH - this is the cell width. This value must be sent only to the HEADER!!!!!!!!
		T_COLOR - text color = array(r,g,b);
		T_SIZE - text size
		T_FONT - text font - font type = "Arial", "Times"
		T_ALIGN - text align - "RLCJ"
		V_ALIGN - text vertical alignment - "TMB"
		T_TYPE - text type (Bold Italic etc)
		LN_SPACE - space between lines
		BG_COLOR - background color = array(r,g,b);
		BRD_COLOR - border color = array(r,g,b);
		BRD_SIZE - border size --
		BRD_TYPE - border size -- up down, with border without!!! etc
		BRD_TYPE_NEW_PAGE - border type on new page - this is user only if specified(<>'')
		TEXT - header text -- THIS ALSO BELONGS ONLY TO THE HEADER!!!!

		all these setting conform to the settings from the multicell functions!!!!
	*/

	/*
	Function: Set_Header_Type($type_arr) -- sets the array for the header type

	type array =
		 array(
			0=>array(
					"WIDTH" => 10,
					"T_COLOR" => array(120,120,120),
					"T_SIZE" => 5,
					...
					"TEXT" => "Header text 1"
				  ),
			1=>array(
					...
				  ),
		 );
	where 0,1... are the column number
	*/

	function Set_Header_Type($type_arr){
		$this->tb_header_type = $type_arr;
	}


	/*
	Characteristics constants for Data Type:
	EVERY CELL FROM THE TABLE IS A MULTICELL
		T_COLOR - text color = array(r,g,b);
		T_SIZE - text size
		T_FONT - text font - font type = "Arial", "Times"
		T_ALIGN - text align - "RLCJ"
		V_ALIGN - text vertical alignment - "TMB"
		T_TYPE - text type (Bold Italic etc)
		LN_SPACE - space between lines
		BG_COLOR - background color = array(r,g,b);
		BRD_COLOR - border color = array(r,g,b);
		BRD_SIZE - border size --
		BRD_TYPE - border size -- up down, with border without!!! etc
		BRD_TYPE_NEW_PAGE - border type on new page - this is user only if specified(<>'')

		all these settings conform to the settings from the multicell functions!!!!
	*/

	/*
	Function: Set_data_Type($type_arr) -- sets the array for the header type

	type array =
		 array(
			0=>array(
					"T_COLOR" => array(120,120,120),
					"T_SIZE" => 5,
					...
					"BRD_TYPE" => 1
				  ),
			1=>array(
					...
				  ),
		 );
	where 0,1... are the column number
	*/

	function Set_Data_Type($type_arr){
		$this->tb_data_type = $type_arr;
	}



	/*
	Function Set_Table_Type

	$type_arr = array(
					"BRD_COLOR"=> array (120,120,120), //border color
					"BRD_SIZE"=>5), //border line width
					"TB_COLUMNS"=>5), //the number of columns
					"TB_ALIGN"=>"L"), //the align of the table, possible values = L, R, C equivalent to Left, Right, Center
					'L_MARGIN' => 0// left margin... reference from this->lmargin values
					)
	*/
	function Set_Table_Type($type_arr){

		if (isset($type_arr['TB_COLUMNS'])) $this->tb_columns = $type_arr['TB_COLUMNS'];
		if (!isset($type_arr['L_MARGIN'])) $type_arr['L_MARGIN']=0;//default values

		$this->tb_table_type = $type_arr;

	}

	//this functiondraws the exterior table border!!!!
	function Draw_Table_Border(){
	/*				"BRD_COLOR"=> array (120,120,120), //border color
					"BRD_SIZE"=>5), //border line width
					"TB_COLUMNS"=>5), //the number of columns
					"TB_ALIGN"=>"L"), //the align of the table, possible values = L, R, C equivalent to Left, Right, Center
	*/

		if ( ! $this->tb_border_draw ) return;

		if ( ! $this->Data_On_Current_Page) return; //there was no data on the current page

		//set the colors
		list($r, $g, $b) = $this->tb_table_type['BRD_COLOR'];
		$this->SetDrawColor($r, $g, $b);

		//set the line width
		$this->SetLineWidth($this->tb_table_type['BRD_SIZE']);

		//draw the border
		$this->Rect(
			$this->table_startx,
			$this->table_starty,
			$this->Get_Table_Width(),
			$this->GetY()-$this->table_starty);

	}

	function End_Page_Border(){
		if (isset($this->tb_table_type['BRD_TYPE_END_PAGE'])){

			if (strpos($this->tb_table_type['BRD_TYPE_END_PAGE'], 'B') >= 0){

				//set the colors
				list($r, $g, $b) = $this->tb_table_type['BRD_COLOR'];
				$this->SetDrawColor($r, $g, $b);

				//set the line width
				$this->SetLineWidth($this->tb_table_type['BRD_SIZE']);

				//draw the line
				$this->Line($this->table_startx, $this->GetY(), $this->table_startx + $this->Get_Table_Width(), $this->GetY());
			}
		}
	}

	//returns the table width in user units
	function Get_Table_Width()
	{
		//calculate the table width
		$tb_width = 0;
		for ($i=0; $i < $this->tb_columns; $i++){
			$tb_width += $this->tb_header_type[$i]['WIDTH'];
		}
		return $tb_width;
	}

	//alignes the table to C, L or R(default is L)
	function Table_Align(){
		//check if the table is aligned
		if (isset($this->tb_table_type['TB_ALIGN'])) $tb_align = $this->tb_table_type['TB_ALIGN']; else $tb_align='';

		//set the table align
		switch($tb_align){
			case 'C':
				$this->SetX($this->lMargin + $this->tb_table_type['L_MARGIN'] + ($this->PageWidth() - $this->Get_Table_Width())/2);
				break;
			case 'R':
				$this->SetX($this->lMargin + $this->tb_table_type['L_MARGIN'] + ($this->PageWidth() - $this->Get_Table_Width()));
				break;
			default:
				$this->SetX($this->lMargin + $this->tb_table_type['L_MARGIN']);
				break;
		}//if (isset($this->tb_table_type['TB_ALIGN'])){
	}

	//Draws the Header
	function Draw_Header(){
		$this->Draw_Header_Command = true;
	}

	//Draws the Header
	function Draw_Header_( $next_line_height = 0 ){

		$this->Table_Align();

		$this->table_startx = $this->GetX();
		$this->table_starty = $this->GetY();

		//if the header will be showed
		if ( ! $this->tb_header_draw ) return;

		$h = 0;

		//calculate the maximum height of the cells
		for($i=0;$i<$this->tb_columns;$i++)
		{

			$this->SetFont(	$this->tb_header_type[$i]['T_FONT'],
							$this->tb_header_type[$i]['T_TYPE'],
							$this->tb_header_type[$i]['T_SIZE']);

			$this->tb_header_type[$i]['CELL_WIDTH'] = $this->tb_header_type[$i]['WIDTH'];

			if (isset($this->tb_header_type[$i]['COLSPAN'])){

				$colspan = (int) $this->tb_header_type[$i]['COLSPAN'];//convert to integer

				for ($j = 1; $j < $colspan; $j++){
					//if there is a colspan, then calculate the number of lines also with the with of the next cell
					if (($i + $j) < $this->tb_columns)
						$this->tb_header_type[$i]['CELL_WIDTH'] += $this->tb_header_type[$i + $j]['WIDTH'];
				}
			}

			$this->tb_header_type[$i]['CELL_LINES'] =
				$this->NbLines($this->tb_header_type[$i]['CELL_WIDTH'],$this->tb_header_type[$i]['TEXT']);

			//this is the maximum cell height
			$h = max($h, $this->tb_header_type[$i]['LN_SIZE'] * $this->tb_header_type[$i]['CELL_LINES']);

//			if (isset($data[$i]['COLSPAN'])){
				//just skip the other cells
//				$i = $i + $colspan - 1;
//			}

		}

		//Issue a page break first if needed
		//calculate the header hight and the next data line hight
		$this->CheckPageBreak($h + $next_line_height, false);

		//Draw the cells of the row
		for($i=0; $i<$this->tb_columns; $i++)
		{
			//border size BRD_SIZE
			$this->SetLineWidth($this->tb_header_type[$i]['BRD_SIZE']);

			//fill color = BG_COLOR
			list($r, $g, $b) = $this->tb_header_type[$i]['BG_COLOR'];
			$this->SetFillColor($r, $g, $b);

			//Draw Color = BRD_COLOR
			list($r, $g, $b) = $this->tb_header_type[$i]['BRD_COLOR'];
			$this->SetDrawColor($r, $g, $b);

			//Text Color = T_COLOR
			list($r, $g, $b) = $this->tb_header_type[$i]['T_COLOR'];
			$this->SetTextColor($r, $g, $b);

			//Set the font, font type and size
			$this->SetFont(	$this->tb_header_type[$i]['T_FONT'],
							$this->tb_header_type[$i]['T_TYPE'],
							$this->tb_header_type[$i]['T_SIZE']);

			//Save the current position
			$x=$this->GetX();
			$y=$this->GetY();

			if ($this->New_Page_Commit){
				if (isset($this->tb_header_type[$i]['BRD_TYPE_NEW_PAGE'])){
					$this->tb_header_type[$i]['BRD_TYPE'] .= $this->tb_header_type[$i]['BRD_TYPE_NEW_PAGE'];
				}
			}

			//Print the text
			$this->MultiCellTable(
					$this->tb_header_type[$i]['CELL_WIDTH'],
					$this->tb_header_type[$i]['LN_SIZE'],
					$this->tb_header_type[$i]['TEXT'],
					$this->tb_header_type[$i]['BRD_TYPE'],
					$this->tb_header_type[$i]['T_ALIGN'],
					$this->tb_header_type[$i]['V_ALIGN'],
					1,
					$h - $this->tb_header_type[$i]['LN_SIZE'] * $this->tb_header_type[$i]['CELL_LINES']
					);

			//Put the position to the right of the cell
			$this->SetXY($x+$this->tb_header_type[$i]['CELL_WIDTH'],$y);

			if (isset($this->tb_header_type[$i]['COLSPAN'])){
				$i = $i + (int)$this->tb_header_type[$i]['COLSPAN'] - 1;
			}


		}

		//Go to the next line
		$this->Ln($h);

		$this->Draw_Header_Command = false;
		$this->New_Page_Commit = false;
		$this->Data_On_Current_Page = true;
	}

	//this function Draws the data's from the table
	//have to call this function after the table initialization, after the table, header and data types are set
	//and after the header is drawed
	/*
	$header = true -> on new page draws the header
			= false - > the header is not drawed
	*/

	function Draw_Data($data, $header = true){

		$h = 0;

		//calculate the maximum height of the cells
		for($i=0; $i < $this->tb_columns; $i++)
		{

			if (!isset($data[$i]['T_FONT'])) $data[$i]['T_FONT'] = $this->tb_data_type[$i]['T_FONT'];
			if (!isset($data[$i]['T_TYPE'])) $data[$i]['T_TYPE'] = $this->tb_data_type[$i]['T_TYPE'];
			if (!isset($data[$i]['T_SIZE'])) $data[$i]['T_SIZE'] = $this->tb_data_type[$i]['T_SIZE'];
			if (!isset($data[$i]['T_COLOR'])) $data[$i]['T_COLOR'] = $this->tb_data_type[$i]['T_COLOR'];
			if (!isset($data[$i]['T_ALIGN'])) $data[$i]['T_ALIGN'] = $this->tb_data_type[$i]['T_ALIGN'];
			if (!isset($data[$i]['V_ALIGN'])) $data[$i]['V_ALIGN'] = $this->tb_data_type[$i]['V_ALIGN'];
			if (!isset($data[$i]['LN_SIZE'])) $data[$i]['LN_SIZE'] = $this->tb_data_type[$i]['LN_SIZE'];
			if (!isset($data[$i]['BRD_SIZE'])) $data[$i]['BRD_SIZE'] = $this->tb_data_type[$i]['BRD_SIZE'];
			if (!isset($data[$i]['BRD_COLOR'])) $data[$i]['BRD_COLOR'] = $this->tb_data_type[$i]['BRD_COLOR'];
			if (!isset($data[$i]['BRD_TYPE'])) $data[$i]['BRD_TYPE'] = $this->tb_data_type[$i]['BRD_TYPE'];
			if (!isset($data[$i]['BG_COLOR'])) $data[$i]['BG_COLOR'] = $this->tb_data_type[$i]['BG_COLOR'];

			$this->SetFont(	$data[$i]['T_FONT'],
							$data[$i]['T_TYPE'],
							$data[$i]['T_SIZE']);

			$data[$i]['CELL_WIDTH'] = $this->tb_header_type[$i]['WIDTH'];

			if (isset($data[$i]['COLSPAN'])){

				$colspan = (int) $data[$i]['COLSPAN'];//convert to integer

				for ($j = 1; $j < $colspan; $j++){
					//if there is a colspan, then calculate the number of lines also with the with of the next cell
					if (($i + $j) < $this->tb_columns)
						$data[$i]['CELL_WIDTH'] += $this->tb_header_type[$i + $j]['WIDTH'];
				}
			}

			$data[$i]['CELL_LINES'] = $this->NbLines($data[$i]['CELL_WIDTH'], $data[$i]['TEXT']);

			//this is the maximum cell height
			$h = max($h, $data[$i]['LN_SIZE'] * $data[$i]['CELL_LINES']);

			if (isset($data[$i]['COLSPAN'])){
				//just skip the other cells
				$i = $i + $colspan - 1;
			}

		}


		$this->CheckPageBreak($h, $header);

		if ($this->Draw_Header_Command){//draw the header
			$this->Draw_Header_($h);
		}

		$this->Table_Align();

		//Draw the cells of the row
		for($i=0;$i<$this->tb_columns;$i++)
		{

			//border size BRD_SIZE
			$this->SetLineWidth($data[$i]['BRD_SIZE']);

			//fill color = BG_COLOR
			list($r, $g, $b) = $data[$i]['BG_COLOR'];
			$this->SetFillColor($r, $g, $b);

			//Draw Color = BRD_COLOR
			list($r, $g, $b) = $data[$i]['BRD_COLOR'];
			$this->SetDrawColor($r, $g, $b);

			//Text Color = T_COLOR
			list($r, $g, $b) = $data[$i]['T_COLOR'];
			$this->SetTextColor($r, $g, $b);

			//Set the font, font type and size
			$this->SetFont(	$data[$i]['T_FONT'],
							$data[$i]['T_TYPE'],
							$data[$i]['T_SIZE']);

			//Save the current position
			$x=$this->GetX();
			$y=$this->GetY();

			//print the text
			$this->MultiCellTable(
					$data[$i]['CELL_WIDTH'],
					$data[$i]['LN_SIZE'],
					$data[$i]['TEXT'],
					$data[$i]['BRD_TYPE'],
					$data[$i]['T_ALIGN'],
					$data[$i]['V_ALIGN'],
					1,
					$h - $data[$i]['LN_SIZE'] * $data[$i]['CELL_LINES']
					);

			//Put the position to the right of the cell
			$this->SetXY($x + $data[$i]['CELL_WIDTH'],$y);

			//if we have colspan, just ignore the next cells
			if (isset($data[$i]['COLSPAN'])){
				$i = $i + (int)$data[$i]['COLSPAN'] - 1;
			}

		}

		$this->Data_On_Current_Page = true;

		//Go to the next line
		$this->Ln($h);
	}

	//if the table is bigger than a page then it jumps to next page and draws the header
	/*
	$h = is the height that if is overriden than the document jumps to a new page
	$header = true/false = this specifies at a new page we write again the header or not. This variable
	is used at the moment when the header draw makes the new page jump
	*/

	function CheckPageBreak($h, $header = true)
	{
		//If the height h would cause an overflow, add a new page immediately
		if($this->GetY()+$h > $this->PageBreakTrigger){

			$this->Draw_Table_Border();//draw the table border

			$this->End_Page_Border();//if there is a special handling for end page??? this is specific for me

			$this->AddPage($this->CurOrientation);//add a new page

			$this->Data_On_Current_Page = false;

			$this->New_Page_Commit = true;//new page commit

			$this->table_startx = $this->GetX();
			$this->table_starty = $this->GetY();
			if ($header) $this ->Draw_Header();//if we have to draw the header!!!
		}

		//align the table
		$this->Table_Align();
	}

	/**   This method returns the number of lines that will a text ocupy on the specified width
	      Call:
	      @param
	                        $w - width
	                        $txt - text
	      @return           number
	*/
	function NbLines($w,$txt)
	{
		//Computes the number of lines a MultiCell of width w will take
		$cw=&$this->CurrentFont['cw'];
		if($w==0)
			$w=$this->w-$this->rMargin-$this->x;
		$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
		$s=str_replace("\r",'',$txt);
		$nb=strlen($s);
		if($nb>0 and $s[$nb-1]=="\n")
			$nb--;
		$sep=-1;
		$i=0;
		$j=0;
		$l=0;
		$nl=1;
		while($i<$nb)
		{
			$c=$s[$i];
			if($c=="\n")
			{
				$i++;
				$sep=-1;
				$j=$i;
				$l=0;
				$nl++;
				continue;
			}
			if($c==' ')
				$sep=$i;
			$l+=$cw[$c];
			if($l>$wmax)
			{
				if($sep==-1)
				{
					if($i==$j)
						$i++;
				}
				else
					$i=$sep+1;
				$sep=-1;
				$j=$i;
				$l=0;
				$nl++;
			}
			else
				$i++;
		}
		return $nl;
	}


	/**   This method allows printing text with line breaks.
	      It works like a modified MultiCell
	      Call:
	      @param
	                        $w - width
	                        $h - line height
	                        $txt - the outputed text
	                        $border - border(LRTB 0 or 1)
	                        $align - horizontal align 'JLR'
	                        $fill - fill (1/0)
	                        $vh - vertical adjustment - the Multicell Height will be with this VH Higher!!!!
	                        $valign - Vertical Alignment - Top, Middle, Bottom
	      @return           nothing
	*/
	function MultiCellTable($w, $h, $txt, $border=0, $align='J', $valign='T', $fill=0, $vh=0)
	{

		$b1 = '';//border for top cell
		$b2 = '';//border for middle cell
		$b3 = '';//border for bottom cell

		if($border)
		{
			if($border==1)
			{
				$border = 'LTRB';
				$b1 = 'LRT';//without the bottom
				$b2 = 'LR';//without the top and bottom
				$b3 = 'LRB';//without the top
			}
			else
			{
				$b2='';
				if(is_int(strpos($border,'L')))
					$b2.='L';
				if(is_int(strpos($border,'R')))
					$b2.='R';
				$b1=is_int(strpos($border,'T')) ? $b2.'T' : $b2;
				$b3=is_int(strpos($border,'B')) ? $b2.'B' : $b2;

			}
		}

		switch ($valign){
			case 'T':
				$wh_T = 0;//Top width
				$wh_B = $vh - $wh_T;//Bottom width
				break;
			case 'M':
				$wh_T = $vh/2;
				$wh_B = $vh/2;
				break;
			case 'B':
				$wh_T = $vh;
				$wh_B = 0;
				break;
			default://default is TOP ALIGN
				$wh_T = 0;//Top width
				$wh_B = $vh - $wh_T;//Bottom width
		}

		//save the X position
		$x = $this->x;
		/*
			if $wh_T == 0 that means that we have no vertical adjustments so I will skip the cells that
			draws the top and bottom borders
		*/

		if ($wh_T != 0)//only when there is a difference
		{
			//draw the top borders!!!
			$this->Cell($w,$wh_T,'',$b1,2,$align,$fill);
		}

		$b2 = is_int(strpos($border,'T')) && ($wh_T == 0) ? $b2.'T' : $b2;
		$b2 = is_int(strpos($border,'B')) && ($wh_B == 0) ? $b2.'B' : $b2;

		$this->MultiCell($w,$h,$txt,$b2,$align,$fill);

		if ($wh_B != 0){//only when there is a difference

			//go to the saved X position
			//a multicell always runs to the begin of line
			$this->x = $x;

			$this->Cell($w, $wh_B, '', $b3, 2, $align,$fill);

			$this->x=$this->lMargin;
		}

	}	
	
	
	
	
	
	
	
	
	//***************************************************************************************************************
	//  LES FONCTIONS AJOUTEES PAR JC CORNIC
	//***************************************************************************************************************

	function setRepere($titre, $posX, $posY, $sizeX, $sizeY, $datasX, $datasY, $droites)
	{

		$espaceX=25;
		$espaceY=30;

		// Si le min=max alors on change le nombre de découpage d'ordonnée
		if ($datasY[0] == $datasY[1])
			if ($datasY[0] == 0) $datasY[2] = 0;
			else $datasY[2] = 1;

		// Le titre
		$this->SetXY($posX+$espaceX, $posY - 10);
		$this->Cell($sizeX , 10, $titre, 0, 2, "C");

		if (($datasY[1]-$datasY[0]) != 0)
			$ratioY = $sizeY/($datasY[1]-$datasY[0]);
		else $ratioY = abs($sizeY/(2*$datasY[0]));
		
		if ($datasY[0] < 0)
			$decalageYNeg = $datasY[0]*$ratioY;
		else $decalageYNeg = 0;


		$this->SetDrawColor(0, 0, 0);
		$this->Line($posX+$espaceX, $posY+$sizeY+$decalageYNeg, $posX+$espaceX+$sizeX, $posY+$sizeY+$decalageYNeg); // X
		$this->Line($posX+$espaceX, $posY+$sizeY, $posX+$espaceX, $posY); // Y

		$this->SetTextColor(0,0,0);
		// Pour l'axe des X
		switch (count($datasX))
		{
			case 1:
				// Mettre la valeur au milieu de l'axe	
				$this->SetXY($posX+$espaceX, $posY + $sizeY);
				$this->Cell($sizeX, 10, $datasX[0], 0, 1, "C");
			
			break;
			case 2:
				// Mettre les deux valeurs en début et fin d'axe	
				$this->Text($posX+$espaceX, $posY + $sizeY + 10, $datasX[0]);	
				$this->Text($posX+$espaceX + $sizeX, $posY + $sizeY + 10, $datasX[1]);	
			break;
			default:
			break;
		}

		// Pour l'axe des Y
		$yPos = $posY + $sizeY;
		$xPos = $posX+$espaceX - 12;
		$value = $datasY[0];
		$yInter = $sizeY / $datasY[2];
		$valueInter = ($datasY[1] - $datasY[0]) / $datasY[2];

		if ($datasY[2] == 5) //**** minY et maxY différents ****//
			for ($i=0 ; $i <= $datasY[2] ; $i++)
			{
				// Mettre les $i valeurs entre le début et la fin de l'axe
				$this->Text($xPos, $yPos, $value);
		
				// Mettre les petites barres correspondantes...
				$this->Line($posX+$espaceX-2, $yPos, $posX+$espaceX+2, $yPos);
			
				$yPos -= $yInter;
				
				if ($i==4) $value=$datasY[1];
				else $value += $valueInter;
			}
		else //**** minY et maxY égaux --> 1 ou 2 intervalles au lieu de 5
		{
			//**** Droite horizontale y=0
			if ($datasY[0] == 0)
			{
				$this->Text($xPos, $yPos, $value);
				$this->Line($posX-2, $yPos, $posX+2, $yPos);		
			}
			else //**** Droite horizontale y=$datasY[0]
			{
				if ($datasY[0] <0)
				{
					//**** Y=$datasY[0] < 0
					$this->Text($xPos, $yPos, $value);
					$this->Line($posX-2, $yPos, $posX+2, $yPos);		
					
					$yPos -= $yInter/2;
					$value = 0;
					
					//**** Y=0
					$this->Text($xPos, $yPos, $value);
					$this->Line($posX-2, $yPos, $posX+2, $yPos);		
				}
				else	
				{
					//**** Y=0
					$this->Text($xPos, $yPos, $value);
					$this->Line($posX-2, $yPos, $posX+2, $yPos);		
		
					//**** Y=$datasY[0] > 0
					$this->Text($xPos, $yPos, $value);
					$this->Line($posX-2, $yPos, $posX+2, $yPos);		
				}		
			}
		}

		// Et on y met les droites...	
		$legendX = $posX+$espaceX + $sizeX/2;
		$legendY = $posY + $sizeY + 20;
		for ($i=0 ; $i<count($droites) ; $i++)
		{
			
//			$j=4*$i+1;
//			$k=4*$i+2;
//			$col=4*$i+3;
//			$l=4*$i+4;

			if ($datasY[0] != $datasY[1])
			{
				$y1 = $posY+$sizeY - ( ($droites[$i][0]-$datasY[0])*$sizeY/($datasY[1]-$datasY[0]));
				$y2 = $posY+$sizeY - ( ($droites[$i][1]-$datasY[0])*$sizeY/($datasY[1]-$datasY[0]));
			}
			else
			{
				$y1 = $posY+$sizeY;
				$y2 = $posY+$sizeY;
			}


			$this->SetDrawColor($droites[$i][2][0], $droites[$i][2][1], $droites[$i][2][2]);
			$this->Line($posX+$espaceX, $y1, $posX+$sizeX, $y2);
			
			// ajouter la légende si elle doit être
			if ($droites[$i][3] != "")
			{
				$this->Line($legendX - 20, $legendY, $legendX - 3, $legendY);
				
				$this->SetTextColor($droites[$i][2][0], $droites[$i][2][1], $droites[$i][2][2]);
				$this->Text($legendX, $legendY, $droites[$i][3]);
				$legendY += 5;
			}
		}	

		// Et on encadre le repere...
		$this->SetDrawColor(0,0,0);
		$espace_Y = 15;
		$this->Line($posX, $posY - $espace_Y, $posX+$espaceX + $sizeX + $espaceX, $posY - $espace_Y); // -Y
		$this->Line($posX+$espaceX + $sizeX + $espaceX, $posY - $espace_Y, $posX+$espaceX + $sizeX + $espaceX, $posY + $sizeY + $espaceY); // +X
		$this->Line($posX+$espaceX + $sizeX + $espaceX, $posY + $sizeY + $espaceY, $posX, $posY + $sizeY + $espaceY); // +Y
		$this->Line($posX, $posY + $sizeY + $espaceY, $posX, $posY - $espace_Y); // -X
	}

	//***********************************************************************************************************
	// Pour écrire un texte dans ue case... [BUI] pour le style de la police et [[LCR]] pour le centrage éventuel
	// Par défault, le texte sera normal et à gauche...
	// Fonction destinée à dessiner un tableau dans un file.pdf
	function drawTableau(&$pdf, $tableType, $headerType, $headerDatas, $datasType, $datas)
	{
		$nbCol = count($headerDatas)/2;

		//we initialize the table class
		$pdf->Table_Init($nbCol, true, true);
		
		//***************************************************************************
		//TABLE HEADER SETTINGS
		//***************************************************************************
		$table_subtype = $tableType;
		$pdf->Set_Table_Type($table_subtype);

		for($i=0; $i<$nbCol; $i++) 
		{
			$header_type[$i] = $headerType;
			$header_type[$i]['WIDTH'] = $headerDatas[$i];

			// Les contenus
			$j = $nbCol+$i;
			$header_type[$i]['TEXT'] = $headerDatas[$j];

			// Si une donnée == 0 alors on affiche rien...
			if ($header_type[$i]['TEXT'] != "0") ;
			else $header_type[$i]['TEXT'] = "";
			
			// par défaut, le texte est centré à gauche, non italic, non souligné et non gras.
			// par défaut, les cellules ne sont pas fusionnées.
			$header_type[$i]['T_TYPE'] = '';
			$header_type[$i]['T_ALIGN'] = '';		
			$header_type[$i]['COLSPAN'] = "1";
		}

		// Si l'utilisateur veut un alignement spécifique pour la première colonne. Sinon, T_ALIGN  prend le dessus...
		if (isset($headerType['T_ALIGN_COL0']))
			$header_type[0]['T_ALIGN'] = $headerType['T_ALIGN_COL0'];

		// Si l'utilisateur veut un fond coloré spécifique  pour la première colonne. Sinon, BG_COLOR  prend le dessus...
		if (isset($headerType['BG_COLOR_COL0']))
			$header_type[0]['BG_COLOR'] = $headerType['BG_COLOR_COL0'];
				
		// Si l'utilisateur précise un type ou un alignement pour une cellule précise du tableau, on l'applique ici
		// Il faut utiliser les balises [I], [B], [U] pour Italic, Bold et Underline
		// Il faut utiliser les balises [L], [C], [R] pour left, centered et rigth
		for($i=0; $i<$nbCol; $i++) 
		{
			if (sscanf($header_type[$i]['TEXT'], "[%[a-zA-Z]]%s", $balise, $reste) != 0)
			{
				//echo "balise = " . $balise;
				if ( (strpos($balise, "I")===FALSE) && (strpos($balise, "B")===FALSE) && (strpos($balise, "U")===FALSE)
				  && (strpos($balise, "L")===FALSE) && (strpos($balise, "C")===FALSE) && (strpos($balise, "R")===FALSE) )
					; // Mauvaise balise ou l'utilisateur veut mettre des crochets dans son tableau, c'est son droit...
				else
				{
					//echo "balise = " . $balise . "<br>";
					// On teste les différentes balises pour ajuster la cellule.
					if (strpos($balise, "I") === FALSE) ;
					else $header_type[$i]['T_TYPE'] .= 'I';
					if (strpos($balise, "B") === FALSE) ;
					else $header_type[$i]['T_TYPE'] .= 'B';
					if (strpos($balise, "U") === FALSE) ;
					else $header_type[$i]['T_TYPE'] .= 'U';
					if (strpos($balise, "L") === FALSE) ;
					else $header_type[$i]['T_ALIGN'] .= 'L';
					if (strpos($balise, "C") === FALSE) ;
					else $header_type[$i]['T_ALIGN'] .= 'C';
					if (strpos($balise, "R") === FALSE) ;
					else $header_type[$i]['T_ALIGN'] .= 'R';
				}
				
				// On supprime la balise du texte de la cellule...
				$header_type[$i]['TEXT'] = str_replace("[".$balise."]", "", $header_type[$i]['TEXT']);
			}
		}
		// Si l'utilsateur ne veut pas de header pour son tableau, il met NULL dans la premiere cellule...
		if ($header_type[0]['TEXT'] == NULL)
		{
			for($i=0; $i<$nbCol; $i++)
			{
				$header_type[$i]['LN_SIZE'] = 0;
				$header_type[$i]['TEXT'] = "";
			}
		}
		

		// Test si l'utilisateur veut fusionner DEUX cellules dans le header de son tableau. Il doit mettre "COLSPAN2" dans la première cellule à fusionner.
		for($i=0 ; $i<$nbCol ; $i++)
		{
			$k=$nbCol+$i;
			$i_1 = $i-1;
			if ( ($k<count($headerDatas)) && ($headerDatas[$k] === "COLSPAN2") )
			{
				$header_type[$i_1]['COLSPAN'] = "2";
				$header_type[$i]['TEXT']= "";
			}
		}

		//set the header type
		$pdf->Set_Header_Type($header_type);
		$pdf->Draw_Header();
		
		//***************************************************************************
		//TABLE DATA SETTINGS
		//***************************************************************************		
		$data_type = Array();//reset the array
		for ($i=0; $i<$nbCol; $i++) $data_type[$i] = $datasType;
		$pdf->Set_Data_Type($data_type);
		
		//*********************************************************************
		// Ce qui suit est valable pour toutes les cellules du tableau (hors header bien entendu).
		//*********************************************************************
		$data = Array();
		for ($i=0 ; $i<count($datas) ; $i+=$nbCol)
		{
			//*********************************************************************
			// Ce qui suit est valable pour la première colonne du tableau
			//*********************************************************************
			// si l'utilisateur a précisé un alignement pour la première colonne, on l'applique ici
			if (isset($datasType['T_ALIGN_COL0']))
				$data[0]['T_ALIGN'] = $datasType['T_ALIGN_COL0'];
				
			// Si l'utilisateur a précisé une couleur de fond pour la première colonne, on l'applique ici.
			if (isset($datasType['BG_COLOR_COL0']))
				$data[0]['BG_COLOR'] = $datasType['BG_COLOR_COL0'];
				
			for ($j=$i ; $j<$i+$nbCol ; $j++)
			{
				$k = $j-$i;
				$data[$k]['TEXT'] = $datas[$j];
				$data[$k]['T_SIZE'] = $datasType['T_SIZE'];
				$data[$k]['LN_SIZE'] = $datasType['LN_SIZE'];
				
				// par défaut, le texte est centré à gauche, non italic, non souligné et non gras.
				// par défaut, les cellules ne sont pas fusionnées.
				$data[$k]['T_TYPE'] = '';
				$data[$k]['T_ALIGN'] = '';		
				$data[$k]['COLSPAN'] = "1";
					
				// Si l'utilisateur a précisé une couleur de fond pour les autres colonnes, on l'applique ici.
				if ( (isset($datasType['BG_COLOR'])) && ($k!=0) )
					$data[$k]['BG_COLOR'] = $datasType['BG_COLOR'];
				
				// Si l'utilisateur précise un type ou un alignement pour une cellule précise du tableau, on l'applique ici
				// Il faut utiliser les balises [I], [B], [U] pour Italic, Bold et Underline
				// Il faut utiliser les balises [L], [C], [R] pour left, centered et rigth
				if (sscanf($data[$k]['TEXT'], "[%[a-zA-Z]]%s", $balise, $reste) != 0)
				{
					//echo "balise = " . $balise;
					if ( (strpos($balise, "I")===FALSE) && (strpos($balise, "B")===FALSE) && (strpos($balise, "U")===FALSE)
					  && (strpos($balise, "L")===FALSE) && (strpos($balise, "C")===FALSE) && (strpos($balise, "R")===FALSE) )
						; // Mauvaise balise ou l'utilisateur veut mettre des crochets dans son tableau, c'est son droit...
					else
					{
						//echo "balise = " . $balise . "<br>";
						// On teste les différentes balises pour ajuster la cellule.
						if (strpos($balise, "I") === FALSE) ;
						else $data[$k]['T_TYPE'] .= 'I';
						if (strpos($balise, "B") === FALSE) ;
						else $data[$k]['T_TYPE'] .= 'B';
						if (strpos($balise, "U") === FALSE) ;
						else $data[$k]['T_TYPE'] .= 'U';
						if (strpos($balise, "L") === FALSE) ;
						else $data[$k]['T_ALIGN'] .= 'L';
						if (strpos($balise, "C") === FALSE) ;
						else $data[$k]['T_ALIGN'] .= 'C';
						if (strpos($balise, "R") === FALSE) ;
						else $data[$k]['T_ALIGN'] .= 'R';
					}
					
					// On supprime la balise du texte de la cellule...
					$data[$k]['TEXT'] = str_replace("[".$balise."]", "", $data[$k]['TEXT']);
				}

				// Si la valeur de la cellule est 0, le choix a été fait ICI de ne rien mettre dans la cellule.
				if ($data[$k]['TEXT'] == "0")
					$data[$k]['TEXT'] ="";
					
				// Test si l'utilisateur veut fusionner deux cellules dans le header de son tableau. Il doit mettre le contenu
				// de la cellule fusionnée dans la première cellule et "COLSPAN2" dans la deuxième cellule.
				if ( ($k<$nbCol) && ($data[$k]['TEXT'] === "COLSPAN2") )
				{
					$k_1 = $k-1;
					$data[$k_1]['COLSPAN'] = "2";
					$data[$k]['TEXT']= "";
				}				
			}
			$pdf->Draw_Data($data);
		}
		
		$pdf->Draw_Table_Border();
	}
}
?>