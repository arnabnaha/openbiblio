<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once('../classes/Lay.php');
require_once('../classes/MemberQuery.php');

class Layout_overdue {
  function render($rpt) {
    list($rpt, $errs) = $rpt->variant_el(array('order_by'=>'member'));
    if (!empty($errs)) {
      Fatal::internalError('Unexpected report error');
    }
    
    $mbrQ = new MemberQuery;
    
    $lay = new Lay('A4');
      $lay->pushFont('Helvetica', 10);
        $lay->container('Columns', array(
          'margin-left'=>'25mm', 'margin-right'=>'25mm',
          'margin-top'=>'35mm', 'margin-bottom'=>'15mm',
        ));
          $mbr = NULL;
          $oldmbr = NULL;
          while ($row = $rpt->each()) {
            if ($row['mbrid'] != $oldmbr) {
              if ($oldmbr !== NULL) {
                $lay->close();
                $lay->container('Columns', array(
                  'margin-left'=>'25mm', 'margin-right'=>'25mm',
                  'margin-top'=>'25mm', 'margin-bottom'=>'25mm',
                ));
              }
              $mbr = $mbrQ->get($row['mbrid']);
              $oldmbr = $row['mbrid'];
              $lay->container('Column', array('margin-left'=>'85mm'));
                $lines = array(
                  OBIB_LIBRARY_NAME,
                  '..',
                  '..',
                  'telefoon: '.OBIB_LIBRARY_PHONE,
                  'geopend: '.OBIB_LIBRARY_HOURS,
                );
                foreach ($lines as $l) {
                  $lay->container('TextLine');
                    $lay->text($l);
                  $lay->close();
                }
              $lay->close();
              $lay->element('Spacer', array('height'=>14));
                $lay->container('Column', array('margin-left'=>'15mm'));
                $lay->container('TextLine');
                  $lay->text($mbr->getFirstName().' '.$mbr->getLastName());
                $lay->close();
                foreach (explode("\n", $mbr->getAddress()) as $l) {
                  $lay->container('TextLine');
                    $lay->text($l);
                  $lay->close();
                }
              $lay->close();
              $lay->element('Spacer', array('height'=>70));
              $lay->container('TextLine');
                $lay->text('Klas: '.$row['school_grade']);
              $lay->close();
              $lay->container('TextLine');
                $lay->text('Mentor: '.$row['school_teacher']);
              $lay->close();
              $lay->container('TextLine');
                $lay->text('Datum: '.date('d-m-Y'));
              $lay->close();
              $lay->element('Spacer', array('height'=>14));
              $lay->container('TextLine');
                $lay->text('Beste '.$mbr->getFirstName().' '.$mbr->getLastName().',');
              $lay->close();
              $lay->element('Spacer', array('height'=>9));
              $lay->container('Paragraph');
                $lay->container('TextLines');
                  $lay->text('Volgens onze gegevens is onderstaande niet op tijd '
                             . 'ingeleverd en aan jou geleend. Bezorg het alsjeblieft zo '
                             . 'snel mogelijk terug en betaal de boete die dan wordt berekend.');
                $lay->close();
              $lay->close();
              $lay->element('Spacer', array('height'=>28));
              $lay->container('TextLine');
                $lay->text('Met vriendelijke groet,');
              $lay->close();
              $lay->element('Spacer', array('height'=>14));
              $lay->container('TextLine');
                $lay->text('De medewerkers van '.OBIB_LIBRARY_NAME);
              $lay->close();
              $lay->element('Spacer', array('height'=>14));
              $lay->pushFont('Times-Italic', 10);
                $lay->container('Line');
                  $lay->container('TextLine', array('width'=>'70mm', 'underline'=>1));
                    $lay->text('Titel');
                  $lay->close();
                  $lay->container('TextLine', array('width'=>'45mm', 'underline'=>1));
                    $lay->text('Auteur');
                  $lay->close();
                  $lay->container('TextLine', array('width'=>'25mm', 'underline'=>1));
                    $lay->text('Inleverdatum');
                  $lay->close();
                  $lay->container('TextLine', array('width'=>'20mm', 'underline'=>1));
                    $lay->text('Dagen te laat');
                  $lay->close();
                $lay->close();
              $lay->popFont();
            }
            $lay->container('Line');
              $lay->container('TextLine', array('width'=>'70mm'));
                $lay->text($row['title']);
              $lay->close();
              $lay->container('TextLine', array('width'=>'45mm'));
                $lay->text($row['author']);
              $lay->close();
              $lay->container('TextLine', array('width'=>'25mm'));
                $lay->text(date('d-m-Y', strtotime($row['due_back_dt'])));
              $lay->close();
              $lay->container('TextLine', array('width'=>'20mm'));
                $lay->text($row['days_late']);
              $lay->close();
            $lay->close();
          }
        $lay->close();
      $lay->popFont();
    $lay->close();
  }
}

?>
