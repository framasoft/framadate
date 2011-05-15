#/==========================================================================
#/
#/Université de Strasbourg - Direction Informatique
#/Auteur : Guilhem BORGHESI
#/Création : Février 2008
#/
#/borghesi@unistra.fr
#/
#/Ce logiciel est régi par la licence CeCILL-B soumise au droit français et
#/respectant les principes de diffusion des logiciels libres. Vous pouvez
#/utiliser, modifier et/ou redistribuer ce programme sous les conditions
#/de la licence CeCILL-B telle que diffusée par le CEA, le CNRS et l'INRIA 
#/sur le site "http://www.cecill.info".
#/
#/Le fait que vous puissiez accéder à cet en-tête signifie que vous avez 
#/pris connaissance de la licence CeCILL-B, et que vous en avez accepté les
#/termes. Vous pouvez trouver une copie de la licence dans le fichier LICENCE.
#/
#/==========================================================================
#/
#/Université de Strasbourg - Direction Informatique
#/Author : Guilhem BORGHESI
#/Creation : Feb 2008
#/
#/borghesi@unistra.fr
#/
#/This software is governed by the CeCILL-B license under French law and
#/abiding by the rules of distribution of free software. You can  use, 
#/modify and/ or redistribute the software under the terms of the CeCILL-B
#/license as circulated by CEA, CNRS and INRIA at the following URL
#/"http://www.cecill.info". 
#/
#/The fact that you are presently reading this means that you have had
#/knowledge of the CeCILL-B license and that you accept its terms. You can
#/find a copy of this license in the file LICENSE.
#/
#/==========================================================================

#!/usr/bin/perl

open (FILE,"../admin/logs_studs.txt");

while (<FILE>) {
  /.*\t(.*u-strasbg.fr)\t.*/;
  print $1."\n";
}
