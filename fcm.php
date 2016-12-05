<?php
  require_once('fungsi/fungsi.php');
  konek_db();

  $cluster = 2; //berapa jumlah klasifikasi yang diinginkan
  $maxIter = 100;
  $exp     = pow(10,-5); //semakin kecil semakin bagus
  $t       = 1; //jumlah iterasi
  $pt[0]   = 0; //fungsi objektif
  $pt[1]   = 0;
  $w       = 2; //syarat w adalah lebih dari 1

  //query untuk seleksi data training :
  $sql = mysql_query("SELECT RED as r, GREEN as g, BLUE as b FROM test;");
  $i = 0;
  while ($dt=mysql_fetch_array($sql)) {
      $j = 0;
      $training[$i][$j] = $dt['r'];
      $j++;
      $training[$i][$j] = $dt['g'];
      $j++;
      $training[$i][$j] = $dt['b'];
      $i++;
  }

  //pembangkitan nilai keanggotaan awal bisa pakai GA biar keren :D
  //tapi karena fix cuman 2 maka bisa langsung di aplikasi pake fungsi

  // $jml_data = count($training[0]);
  // print($jml_data);
  // print("\n");
  $jumlah_data_training = count($training);
  $jumlah_fitur = count($training[0]);

  for ($i=0; $i < $jumlah_data_training; $i++) {
        $j=0;
        $derajat_keanggotaan[$i][$j] = rand(1,9)/10;
        $j++;
        $derajat_keanggotaan[$i][$j] = 1 - $derajat_keanggotaan[$i][$j-1];

  }

  // for ($i=0; $i < $jumlah_data_training; $i++) {
  //     for ($j=0; $j < $cluster; $j++) {
  //         print($derajat_keanggotaan[$i][$j]);
  //         print(" :: ");
  //
  //     }
  //     print("\n");
  // }

  //definisikan total nilai pw, pw_red, pw_green, pw_blue = 0;
  for ($i=0; $i < $cluster ; $i++) {
      $total_pw[$i]=0;
      for ($j=0; $j < $jumlah_fitur; $j++) {
        $total_pw_color[$i][$j]=0;
      }
  }

  for ($j=0; $j < $jumlah_fitur; $j++) {
    $temp[$j]=0;
  }

  //proses perhitungan pusat cluster:
  for ($i=0; $i < $cluster; $i++) {
    for ($j=0; $j < $jumlah_data_training; $j++) {
        $pw[$i][$j] = pow($derajat_keanggotaan[$j][$i],$w); //derajat keanggotaan di pangkatkan dengan w
        for ($k=0; $k < count($training[0]); $k++) {
          $pw_color[$i][$j][$k] = $training[$j][$k] * $pw[$i][$j];
          $temp[$k] = $temp[$k] + $pw_color[$i][$j][$k]; //menjumlah nilai tiap warna
        }

        $total_pw[$i] = $total_pw[$i] + $pw[$i][$j];

      }

    for ($k=0; $k < $jumlah_fitur; $k++) {
        $total_pw_color[$i][$k]=$temp[$k]; //menjadikan dalam satu variable
    }

  }

  //menentukan pusat cluster :
  for ($i=0; $i < $cluster; $i++) {
    for ($j=0; $j < $jumlah_fitur; $j++) {
      $pusat_cluster[$i][$j] = $total_pw_color[$i][$j]/$total_pw[$i];
    }
  }

  //selanjutnya adalah menghitung fungsi objecktiv
  //inisialisasi f_obj[][]=;
  for ($i=0; $i < $jumlah_data_training; $i++) {
    # code...
    for ($j=0; $j < $cluster; $j++) {
      # code...
      $f_obj[$i][$j]=0;
    }
  }

  $sum_f_obj=0;
  for ($i=0; $i < $jumlah_data_training; $i++) {
    $sum_tmp_f_obj=0;
    for ($j=0; $j < $cluster; $j++) {
      $tmp_f_obj=0;
      for ($k=0; $k < $jumlah_fitur; $k++) {
        # code...
        $tmp_f_obj=$tmp_f_obj+ (pow(($training[$i][$k]-$pusat_cluster[$j][$k]),$w)); //menghitung satu fungsi objectiv (L1 // L2)
      }
      $f_obj[$i][$j]=$tmp_f_obj;
      $sum_tmp_f_obj= $sum_tmp_f_obj + $f_obj[$i][$j]; // jumlah L1 dan L2
    }
    $sum_f_obj = $sum_f_obj + $sum_tmp_f_obj; //jumlah keseluruhan fungsi objectiv
  }

  print($sum_f_obj);
  print("\n");


 ?>
