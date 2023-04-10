<?php
require('fpdf.php');
define('EURO', chr(128) );
define('EURO_VAL', 6.55957 );

// Nicolas Poupard 2020
// Version 1.00
//////////////////////////////////////
// fonctions à utiliser (publiques) //
//////////////////////////////////////
//  function sizeOfText( $texte, $larg )
//  function addLogo( $img )
//  function addSociete( $nom, $adresse )
//  function addFactureInfo( $numfact, $datefact, $person, $phone, $mail, $person_id, $vat_num )
//  function addClientAdresse( $adresse )
//  function addCols( $tab )
//  function addLineFormat( $tab )
//  function addLine( $ligne, $tab )
//  function addColEnd( )
//  function addRemarque($remarque)
//  function addCadreTotal()

class PDF_Invoice extends FPDF
{
	// variables privées
	var $colonnes;
	var $format;
	var $angle=0;
	
	// fonctions privées
	function RoundedRect($x, $y, $w, $h, $r, $style = '')
	{
		$k = $this->k;
		$hp = $this->h;
		if($style=='F')
			$op='f';
		elseif($style=='FD' || $style=='DF')
			$op='B';
		else
			$op='S';
		$MyArc = 4/3 * (sqrt(2) - 1);
		$this->_out(sprintf('%.2F %.2F m',($x+$r)*$k,($hp-$y)*$k ));
		$xc = $x+$w-$r ;
		$yc = $y+$r;
		$this->_out(sprintf('%.2F %.2F l', $xc*$k,($hp-$y)*$k ));

		$this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);
		$xc = $x+$w-$r ;
		$yc = $y+$h-$r;
		$this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-$yc)*$k));
		$this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);
		$xc = $x+$r ;
		$yc = $y+$h-$r;
		$this->_out(sprintf('%.2F %.2F l',$xc*$k,($hp-($y+$h))*$k));
		$this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);
		$xc = $x+$r ;
		$yc = $y+$r;
		$this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$yc)*$k ));
		$this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
		$this->_out($op);
	}

	function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
	{
		$h = $this->h;
		$this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1*$this->k, ($h-$y1)*$this->k,
							$x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
	}

	function Rotate($angle, $x=-1, $y=-1)
	{
		if($x==-1)
			$x=$this->x;
		if($y==-1)
			$y=$this->y;
		if($this->angle!=0)
			$this->_out('Q');
		$this->angle=$angle;
		if($angle!=0)
		{
			$angle*=M_PI/180;
			$c=cos($angle);
			$s=sin($angle);
			$cx=$x*$this->k;
			$cy=($this->h-$y)*$this->k;
			$this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
		}
	}

	function _endpage()
	{
		if($this->angle!=0)
		{
			$this->angle=0;
			$this->_out('Q');
		}
		parent::_endpage();
	}

	// fonctions publiques
	
	function sizeOfText( $texte, $largeur )
	{
		$index    = 0;
		$nb_lines = 0;
		$loop     = TRUE;
		while ( $loop )
		{
			$pos = strpos($texte, "\n");
			if (!$pos)
			{
				$loop  = FALSE;
				$ligne = $texte;
			}
			else
			{
				$ligne  = substr( $texte, $index, $pos);
				$texte = substr( $texte, $pos+1 );
			}
			$length = floor( $this->GetStringWidth( $ligne ) );
			if($largeur)
			$res = 1 + floor( $length / $largeur) ;
			else
			$res = 1;	
			$nb_lines += $res;
		}
		return $nb_lines;
	}
	
	// Cette fonction affiche en haut, a droite le logo
	function addLogo( $img)
	{
		$x1  = $this->w - 75;
		$y1 = 8;
		$this->Image($img,$x1,$y1);
		$this->SetXY( $x1, $y1 + 25 );
	}
	
	// Cette fonction affiche en haut, a gauche,
	// le nom de la societe dans la police Arial-12-Bold
	// les coordonnees de la societe dans la police Arial-10
	function addSociete( $nom, $adresse )
	{
		$x1 = 10;
		$y1 = 50;
		//Positionnement en bas
		$this->SetXY( $x1, $y1 );
		$this->SetFont('Arial','B',9);
		$length = $this->GetStringWidth( $nom );
		$this->Cell( $length, 2, $nom);
		$this->SetXY( $x1, $y1 + 3 );
		$this->SetFont('Arial','',8);
		$length = $this->GetStringWidth( $adresse );
		//Coordonnées de la société
		$lignes = $this->sizeOfText( $adresse, $length) ;
		$this->MultiCell($length, 3, $adresse);
	}
	
	// Affiche un cadre avec les infos de la facture
	// (en haut, a droite)
	function addFactureInfo( $numfact, $datefact, $person, $phone, $mail, $person_id, $info, $datedue )
	{
		
		$r1  = $this->w - 95;
		$r2  = $r1 + 35;
		$y1  = 33.5;
		$y2  = $y1;
		$this->SetXY( $r1, $y1 );
		$this->SetFont( "Arial", "B", 10);
		$this->Cell($r1,$y1, "INVOICE", 0, 0, "L");
		$y2+=5;
		
		$this->SetFont( "Arial", "", 8);
		
		$this->SetXY( $r1, $y2 );
		$this->Cell($r1,$y1, "No.:", 0, 0, "L");
		$this->SetXY( $r2, $y2 );
		$this->Cell($r2,$y1, $numfact, 0, 0, "L");
		$y2+=3;
		
		$this->SetXY( $r1, $y2 );
		$this->Cell($r1,$y1, "Date:", 0, 0, "L");
		$this->SetXY( $r2, $y2 );
		$this->Cell($r2,$y1, $datefact, 0, 0, "L");
		$y2+=3;
		
		/*$this->SetXY( $r1, $y2 );
		$this->Cell($r1,$y1, "Payment condition:", 0, 0, "L");
		$this->SetXY( $r2, $y2 );
		$this->Cell($r2,$y1, $datedue, 0, 0, "L");
		$y2+=3;*/
		
		if($person){
			$this->SetXY( $r1, $y2 );
			$this->Cell($r1,$y1, "Person:", 0, 0, "L");
			$this->SetXY( $r2, $y2 );
			$this->Cell($r2,$y1, $person, 0, 0, "L");
			$y2+=3;
		}
		
		if($phone){
			$this->SetXY( $r1, $y2 );
			$this->Cell($r1,$y1, "Tel no.:", 0, 0, "L");
			$this->SetXY( $r2, $y2 );
			$this->Cell($r2,$y1, $phone, 0, 0, "L");
			$y2+=3;
		}
		
		if($mail){
			$this->SetXY( $r1, $y2 );
			$this->Cell($r1,$y1, "e-Mail:", 0, 0, "L");
			$this->SetXY( $r2, $y2 );
			$this->Cell($r2,$y1, $mail, 0, 0, "L");
			$y2+=5;
		}
		
		$this->SetXY( $r1, $y2 );
		$this->Cell($r1,$y1, "Customer no.:", 0, 0, "L");
		$this->SetXY( $r2, $y2 );
		$this->Cell($r2,$y1, $person_id, 0, 0, "L");
		$y2+=3;
		
		$this->SetXY( $r1, $y2 );
		$this->Cell($r1,$y1, "VAT no:", 0, 0, "L");
		$this->SetXY( $r2, $y2 );
		$this->Cell($r2,$y1, $info, 0, 0, "L");
		$y2+=5;
		

	}
	
	// Affiche l'adresse du client
	// (en haut, a droite)
	function addClientAdresse( $adresse )
	{
		$r1     = 10;
		$r2     = $r1 + 68;
		$y1     = 75;
		$this->SetXY( $r1, $y1 );
		$this->SetFont( "Arial", "U", 8);
		$this->Cell($r2,$y1-75, "Customer address:", 0, 0, "L");
		$this->SetFont( "Arial", "", 8);
		$this->SetXY( $r1, $y1+3);
		$this->MultiCell( 60, 3, utf8_decode($adresse));
	}
	
	// trace le cadre des colonnes du devis/facture
	function addCols( $tab )
	{
		global $colonnes;

		$r1  = 10;
		$r2  = $this->w - ($r1 * 2) ;
		$y1  = 100;
		$y2  = $this->h - 50 - $y1;
		$this->SetXY( $r1, $y1 );
		//$this->Rect( $r1, $y1, $r2, $y2, "D");
		$this->Line( $r1, $y1+6, $r1+$r2, $y1+6);
		$colX = $r1;
		$colonnes = $tab;
		while ( list( $lib, $pos ) = each ($tab) )
		{
			$this->SetXY( $colX, $y1+2 );
			if($lib == 'Item' || $lib == 'Pos.')
				$this->Cell( $pos, 1, $lib, 0, 0, "L");
			else
				$this->Cell( $pos, 1, $lib, 0, 0, "R");
			$colX += $pos;
			//$this->Line( $colX, $y1, $colX, $y1+$y2);
		}
	}
	
	// trace le cadre des colonnes du devis/facture
	function addCols2( $tab )
	{
		global $colonnes;

		$r1  = 10;
		$r2  = $this->w - ($r1 * 2) ;
		$y1  = 20;
		$y2  = $this->h - 50 - $y1;
		$this->SetXY( $r1, $y1 );
		//$this->Rect( $r1, $y1, $r2, $y2, "D");
		$this->Line( $r1, $y1+6, $r1+$r2, $y1+6);
		$colX = $r1;
		$colonnes = $tab;
		while ( list( $lib, $pos ) = each ($tab) )
		{
			$this->SetXY( $colX, $y1+2 );
			$this->Cell( $pos, 1, $lib, 0, 0, "C");
			$colX += $pos;
			//$this->Line( $colX, $y1, $colX, $y1+$y2);
		}
	}
	
	// mémorise le format (gauche, centre, droite) d'une colonne
	function addLineFormat( $tab )
	{
		global $format, $colonnes;

		while ( list( $lib, $pos ) = each ($colonnes) )
		{
			if ( isset( $tab["$lib"] ) )
				$format[ $lib ] = $tab["$lib"];
		}
	}
	
	// Affiche chaque "ligne" d'un devis / facture
	function addLine( $ligne, $tab )
	{
		global $colonnes, $format;

		$ordonnee     = 10;
		$maxSize      = $ligne;

		reset( $colonnes );
		while ( list( $lib, $pos ) = each ($colonnes) )
		{
			$longCell  = $pos -2;
			$texte     = $tab[ $lib ];
			$length    = $this->GetStringWidth( $texte );
			$tailleTexte = $this->sizeOfText( $texte, $length );
			$formText  = $format[ $lib ];
			$this->SetXY( $ordonnee, $ligne-1);
			$this->MultiCell( $longCell, 4 , $texte, 0, $formText);
			if ( $maxSize < ($this->GetY()  ) )
				$maxSize = $this->GetY() ;
			$ordonnee += $pos;
		}
		return ( $maxSize - $ligne );
	}
	// trace le cadre des colonnes du devis/facture
	function addColEnd( $y = 0 )
	{
		global $colonnes;
		if(!$y)$y = $this->GetY();
		$this->SetXY( $this->w, $y);
		$r1  = 10;
		$r2  = $this->w - ($r1 * 2) ;
		$y1  = $this->y;
		$y2  = $this->h - 50 - $y1;
		$this->SetXY( $r1, $y1 );
		//$this->Rect( $r1, $y1, $r2, $y2, "D");
		$this->Line( $r1, $y1+6, $r1+$r2, $y1+6);
		return $y1+6;
	}
	
	// Ajoute une remarque (en bas, a gauche)
	function addRemarque($conditions = '',$remarque,$y = 0)
	{
		global $format;
		if(!$y)$y = $this->GetY();
		//$this->SetXY( $this->w, $y);
		$remarque = str_replace('$euro$',EURO,utf8_decode($remarque));
		$cut_rem = explode('<br />',$remarque);
		$rem = '';
		foreach($cut_rem as $cut){
			$rem .= $cut."\n";
		}
		$remarque = $rem;
		
		if($conditions){
			$conditions = str_replace('$euro$',EURO,utf8_decode($conditions));
			$cut_cond = explode('<br />',$conditions);
			$cond = '';
			foreach($cut_cond as $cut2){
				$cond .= $cut2."\n";
			}
			$remarque = 'Payment conditions: '.$cond."\n".$remarque;
		}
		$this->SetFont( "Arial", "", 8);
		$length = $this->GetStringWidth( $remarque );
		$r1  = 10;
		$r2  = $r1 + $length;
		$y1  = $y;
		$y2  = $y1+20;
		$this->SetXY( $r1 , $y1 );
		$this->MultiCell( 0, 3 , $remarque, 0,null);
	}
	
	
	// trace le cadre des totaux
	function addCadreTotal($y=0,$label_tva = 'VAT', $is_tva = true, $deposit = 0, $amount = 0, $vat = 0, $total = 0, $currency = 'EUR')
	{
		
		$r1  = $this->w - 50;
		$r2  = $r1 + 40;
		$y1  = $y;//$this->h - 45;
		$y2  = 0;
		$this->SetXY( $r1, $y1 );
		$this->SetFont( "Arial", "", 7);
		$this->Cell(25,0, "Net Amount:", 0, 0, "L");
		$this->SetFont( "Arial", "", 8);
		$this->Cell(15,$y2, $amount, 0, 0, "R");
		$this->Line( $r1,  $this->GetY()+3, $r2, $this->GetY()+3);
		
		$y1+=7;
		$this->SetXY( $r1, $y1 );
		$this->SetFont( "Arial", "", 7);
		$this->Cell(25,0, $label_tva.":", 0, 0, "L");
		$this->SetFont( "Arial", "", 8);
		$this->Cell(15,0, $vat, 0, 0, "R");
		$this->Line( $r1,  $this->GetY()+3, $r2, $this->GetY()+3);
		
		$y1+=7;
		$this->SetXY( $r1, $y1 );
		$this->SetFont( "Arial", "", 7);
		$this->Cell(25,0, "Total amount (".''.$currency.'):', 0, 0, "L");
		$this->SetFont( "Arial", "", 8);
		$this->Cell(15,0, $total, 0, 0, "R");
		
		
		
		if($deposit >0){
			$this->Line( $r1,  $this->GetY()+3, $r2, $this->GetY()+3);
			
			$y1+=7;
			$this->SetXY( $r1, $y1 );
			$this->SetFont( "Arial", "", 7);
			$this->Cell(25,0, "Deposit:", 0, 0, "L");
			$this->SetFont( "Arial", "", 8);
			$this->Cell(15,0, $deposit, 0, 0, "R");
			$this->Line( $r1,  $this->GetY()+3, $r2, $this->GetY()+3);
			
			$y1+=7;
			$this->SetXY( $r1, $y1 );
			$this->SetFont( "Arial", "", 7);
			$this->Cell(25,0, "Total due:", 0, 0, "L");
			$this->SetFont( "Arial", "", 8);
			$this->Cell(15,0, number_format(str_replace(',','',$total) - str_replace(',','',$deposit),2,'.',','), 0, 0, "R");
		}
	}
	
	// Page footer
	function Footer($text = '')
	{
		// Position at 1.5 cm from bottom
		$this->SetY(-30);
		// Arial italic 8
		$this->SetFont('Arial','',6);
		$this->MultiCell( 0, 3 , $text, 0,null);
		//$this->Cell(0,10,$text,0,0,'C');
		// Page number
		//$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	}
	
}
?>