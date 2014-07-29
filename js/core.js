$(document).ready(function() {
	// ajout d'un bg-color correspondant au vote dans le formulaire
    $('.yes input').change(function(){
        if($(this).is(':checked')){
            $(this).parent('li').parent('ul').parent('td').attr('class', 'bg-success');
        };
    });
    $('.ifneedbe input').change(function(){
        if($(this).is(':checked')){
            $(this).parent('li').parent('ul').parent('td').attr('class', 'bg-warning');
        };
    });
    $('.no input').change(function(){
        if($(this).is(':checked')){
            $(this).parent('li').parent('ul').parent('td').attr('class', 'bg-danger');
        };
    });
    $('.yes input:checked').parent('li').parent('ul').parent('td').attr('class', 'bg-success');
    $('.ifneedbe input:checked').parent('li').parent('ul').parent('td').attr('class', 'bg-warning');
    $('.no input:checked').parent('li').parent('ul').parent('td').attr('class', 'bg-danger');
    
    // Date/Sujets + Sondés toujours visibles
    //$('table.results').fixedHeaderTable({ footer: false, cloneHeadToFoot: false, fixedColumn: true });
    
});
