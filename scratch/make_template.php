<?php
require 'vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

$phpWord = new PhpWord();
$section = $phpWord->addSection();

// Judul
$section->addText('SURAT KETERANGAN DOMISILI', ['bold' => true, 'size' => 16], ['alignment' => 'center']);
$section->addTextBreak(1);

// Isi
$section->addText('Yang bertanda tangan di bawah ini menerangkan bahwa:', ['size' => 11]);
$section->addTextBreak(1);

$section->addText('Nama : ${nama}', ['size' => 11]);
$section->addText('NIK  : ${nik}', ['size' => 11]);
$section->addText('Alamat: ${alamat}', ['size' => 11]);

$section->addTextBreak(2);
$section->addText('Demikian surat ini dibuat untuk dipergunakan sebagaimana mestinya.', ['size' => 11]);

$section->addTextBreak(2);
$section->addText('Cibatu, ' . date('d F Y'), ['size' => 11], ['alignment' => 'right']);
$section->addTextBreak(3);
$section->addText('Kepala Desa Cibatu', ['bold' => true, 'size' => 11], ['alignment' => 'right']);

// Simpan ke folder template
$path = 'storage/app/templates/surat/template_domisili.docx';
$objWriter = IOFactory::createWriter($phpWord, 'Word2007');
$objWriter->save($path);

echo "Template berhasil dibuat di: " . $path . "\n";
