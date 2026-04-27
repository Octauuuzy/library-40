-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 30, 2026 at 05:45 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `perpustakaans`
--

-- --------------------------------------------------------

--
-- Table structure for table `buku`
--

CREATE TABLE `buku` (
  `id_buku` int NOT NULL,
  `id_kategori` int NOT NULL,
  `judul` varchar(255) NOT NULL,
  `penulis` varchar(255) NOT NULL,
  `tahun_terbit` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `stok` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `buku`
--

INSERT INTO `buku` (`id_buku`, `id_kategori`, `judul`, `penulis`, `tahun_terbit`, `deskripsi`, `stok`) VALUES
(2, 2, 'Dummy', 'Dummy', '1945', 'Dummy', 1),
(3, 2, 'The Walking Dead', 'Robert Kirkman', '2003', 'sinopsis komik The Walking Dead berpusat pada perjalanan Rick Grimes, seorang deputi sheriff yang terbangun dari koma di sebuah rumah sakit yang terbengkalai. Ia menemukan dunia telah hancur dan dipenuhi oleh mayat hidup yang haus darah, yang dikenal sebagai \"walkers\".\r\nSetelah berhasil menemukan istri dan anaknya di sebuah kamp pengungsi di luar Atlanta, Rick perlahan-lahan dipaksa menjadi pemimpin bagi sekelompok penyintas. Cerita ini tidak hanya tentang bertahan hidup dari serangan zombie, tetapi lebih dalam tentang bagaimana manusia beradaptasi, berubah, dan sering kali kehilangan kemanusiaannya demi tetap hidup di dunia tanpa hukum.\r\nSeiring berjalannya waktu, Rick dan kelompoknya berpindah dari satu tempat ke tempat lain—mulai dari penjara yang aman hingga komunitas Alexandria yang terisolasi. Namun, ancaman terbesar yang mereka hadapi bukanlah zombie, melainkan kelompok manusia lain yang kejam seperti pengikut The Governor dan geng The Saviors yang dipimpin oleh Negan.', 1),
(4, 3, 'Think and Grow Rich', 'Napoleon Hill', '1937', 'Buku ini menantang pandangan konvensional bahwa sukses hanyalah soal kerja keras atau keberuntungan. Hill memperkenalkan 13 prinsip filosofis yang dimulai dari Hasrat (Desire) yang membara. Ia menjelaskan bahwa pikiran bawah sadar manusia bisa diprogram melalui Autosaran dan Visualisasi untuk menarik peluang finansial.\r\n\r\nSalah satu konsep paling revolusioner dalam buku ini adalah \"Master Mind\", yaitu kekuatan yang muncul ketika dua orang atau lebih bekerja sama secara harmonis demi satu tujuan. Hill menekankan bahwa kegagalan hanyalah ujian sementara, dan kunci pembedanya adalah Ketabahan (Persistence) untuk terus maju saat orang lain berhenti.', 1),
(5, 3, 'The Uncomfortable Truth About Money: How to Live with Uncertainty and Think for Yourself', 'Paul Padolsky', '2024', 'Buku The Uncomfortable Truth About Money karya Paul Podolsky merupakan sebuah refleksi tajam yang mengajak pembaca untuk berhenti mengejar kepastian palsu dalam dunia keuangan. Podolsky berargumen bahwa ketakutan kita terhadap uang sering kali bersumber dari ketidaktahuan dan ketergantungan pada \"pakar\" yang sebenarnya juga tidak bisa memprediksi masa depan. Melalui sudut pandang seorang investor veteran, ia mengibaratkan literasi keuangan seperti keterampilan dasar bertahan hidup; jika seseorang tidak mampu memahami mekanisme uang secara mandiri, ia akan selamanya terjebak dalam kecemasan.\r\n\r\nAlih-alih memberikan rumus matematika yang rumit, buku ini menekankan pentingnya kesehatan mental dan ketenangan batin dalam mengelola aset. Penulis menyoroti bahwa kunci menghadapi ekonomi yang kacau bukanlah dengan mencoba mengontrol pasar, melainkan dengan mengontrol perilaku diri sendiri dan membangun bantalan keamanan yang masuk akal. Pada akhirnya, Podolsky menantang pembaca untuk meruntuhkan asumsi-asumsi umum dan mulai berpikir jernih demi mencapai kebebasan yang sesungguhnya—bukan sekadar angka di rekening bank, melainkan kemampuan untuk tetap tegak di tengah ketidakpastian global.', 1),
(6, 4, 'The Laws of Human Nature', 'Robert Greene', '2018', 'Buku The Laws of Human Nature karya Robert Greene merupakan sebuah penjelajahan mendalam terhadap dorongan psikologis yang sering kali tidak kita sadari namun mengendalikan hampir setiap tindakan kita. Greene berargumen bahwa manusia pada dasarnya adalah makhluk emosional yang sering kali terperangkap dalam pola perilaku kuno hasil evolusi, seperti rasa iri, narsisme, dan agresivitas. Melalui buku ini, pembaca diajak untuk menanggalkan ilusi bahwa kita adalah makhluk yang sepenuhnya rasional dan mulai melihat kenyataan pahit tentang sifat asli manusia.\r\nNarasi dalam buku ini dibangun untuk membantu seseorang mengembangkan \"kekuatan pengamatan\" yang tajam agar bisa melihat di balik topeng sosial yang dikenakan orang lain setiap hari. Dengan memahami motivasi tersembunyi tersebut, seseorang tidak hanya bisa melindungi diri dari individu yang manipulatif atau toksik, tetapi juga belajar mengendalikan dorongan irasional dalam diri sendiri. Greene menggunakan berbagai kisah sejarah dari tokoh dunia untuk mengilustrasikan bagaimana penguasaan terhadap hukum-hukum alamiah ini dapat mengubah kelemahan menjadi keunggulan strategis dalam hubungan sosial, karier, dan kepemimpinan.', 1),
(7, 4, 'The Psychology of Money', 'Morgan Housel', '2020', 'Buku The Psychology of Money karya Morgan Housel mengupas sisi manusiawi dari uang yang sering kali terabaikan dalam buku teks ekonomi tradisional. Alih-alih berfokus pada grafik atau rumus matematika yang rumit, Housel menjelaskan bahwa kesuksesan finansial lebih banyak ditentukan oleh perilaku dan karakter seseorang daripada tingkat kecerdasan atau kemampuan teknisnya. Penulis membawa pembaca memahami bahwa keputusan keuangan sering kali tidak dibuat di atas lembar kerja Excel, melainkan di meja makan atau dalam ruang rapat di mana emosi, ego, pandangan dunia yang unik, serta faktor keberuntungan saling bercampur baur.\r\nMelalui kumpulan cerita pendek dan observasi yang tajam, buku ini menekankan betapa pentingnya kerendahan hati dalam menghadapi risiko serta kesabaran dalam memanfaatkan kekuatan bunga berbunga. Housel membedakan dengan jelas antara menjadi kaya, yang sering kali hanya tentang pamer kemewahan, dan menjadi makmur, yang berarti memiliki kendali penuh atas waktu dan kebebasan hidup. Pada akhirnya, buku ini menawarkan perspektif bahwa mengelola uang dengan baik adalah tentang memahami keterbatasan diri sendiri sebagai manusia, menetapkan definisi kata \"cukup,\" dan membangun ketahanan mental agar tetap konsisten dalam rencana keuangan jangka panjang meski dunia sedang penuh ketidakpastian.', 1),
(8, 5, 'Laskar Awan', 'Andrea Kirata', '2005', 'Laskar Pelangi mengisahkan perjuangan sepuluh anak di Desa Gantung, Pulau Belitung, yang berusaha menempuh pendidikan di SD Muhammadiyah, sebuah sekolah dasar yang kondisinya sangat memprihatinkan dan nyaris roboh. Cerita dimulai dengan ketegangan saat sekolah tersebut terancam ditutup oleh pemerintah jika tidak berhasil menjaring minimal sepuluh murid baru. Beruntung, kehadiran seorang anak bernama Harun di menit-menit terakhir menyelamatkan sekolah tersebut dari penutupan.\r\nDi bawah bimbingan dua guru yang sangat berdedikasi, Ibu Muslimah dan Pak Harfan, kesepuluh anak yang kemudian dijuluki sebagai \"Laskar Pelangi\" ini belajar tentang persahabatan, keberanian, dan kekuatan mimpi di tengah kemiskinan yang mencekik. Meskipun mereka harus menghadapi tekanan dari perusahaan timah besar (PN Timah) yang mendominasi pulau tersebut, mereka membuktikan bahwa keterbatasan ekonomi bukanlah penghalang untuk memiliki kecerdasan dan cita-cita yang tinggi. Melalui sudut pandang tokoh Ikal, pembaca dibawa menyelami dinamika emosional anak-anak pesisir yang gigih melawan nasib demi masa depan yang lebih baik.', 5);

-- --------------------------------------------------------

--
-- Table structure for table `favorit`
--

CREATE TABLE `favorit` (
  `id_favorit` int NOT NULL,
  `id_user` int NOT NULL,
  `id_buku` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `favorit`
--

INSERT INTO `favorit` (`id_favorit`, `id_user`, `id_buku`) VALUES
(9, 3, 8);

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int NOT NULL,
  `nama_kategori` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`) VALUES
(2, 'Action'),
(3, 'Finance'),
(4, 'Psychology'),
(5, 'Drama'),
(7, 'Fantasy');

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id_peminjaman` int NOT NULL,
  `id_user` int NOT NULL,
  `id_buku` int NOT NULL,
  `tanggal_peminjaman` date NOT NULL,
  `tanggal_pengembalian` date NOT NULL,
  `tanggal_dikembalikan` date DEFAULT NULL,
  `denda` int NOT NULL DEFAULT '0',
  `status_peminjaman` enum('dipinjam','dikembalikan') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `peminjaman`
--

INSERT INTO `peminjaman` (`id_peminjaman`, `id_user`, `id_buku`, `tanggal_peminjaman`, `tanggal_pengembalian`, `tanggal_dikembalikan`, `denda`, `status_peminjaman`) VALUES
(3, 2, 8, '2026-03-12', '2026-03-19', NULL, 0, 'dipinjam');

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

CREATE TABLE `setting` (
  `id_setting` int NOT NULL,
  `nama` varchar(255) NOT NULL,
  `nilai` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`id_setting`, `nama`, `nilai`) VALUES
(1, 'toleransi_hari', '1'),
(2, 'denda_per_hari', '5000');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int NOT NULL,
  `nama` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `no_handphone` int DEFAULT NULL,
  `level` enum('admin','anggota') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `nama`, `username`, `password`, `email`, `alamat`, `no_handphone`, `level`) VALUES
(1, 'Administrator', 'admin', '$2y$10$p9F.Rqpy8Jjf3Srh2tvmt.An5gcyRfVr9rdr.AnsOLApJD3cnBzk.', 'admin@perpustakaan.com', 'Perpustakaan', 0, 'admin'),
(2, 'Prabowo Subinato', 'prabowosubinato.official', '$2y$10$jB8Hnxp5OEexWjMixhdMuunr3LPCu5LYlFnKDrT.YACDamjELzN0S', 'nato.subinato@nato.gov', 'Jl. Kemayoran', 911, 'anggota'),
(3, 'Octaa', 'octaa', '$2y$10$lwoNzZPJLi8mnXQn8Sdylu7qKHxO.iadA.FTLa4UlK/W5CgPOMrDG', 'octa@projectocta.me', '-', 911, 'anggota');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `buku`
--
ALTER TABLE `buku`
  ADD PRIMARY KEY (`id_buku`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indexes for table `favorit`
--
ALTER TABLE `favorit`
  ADD PRIMARY KEY (`id_favorit`),
  ADD UNIQUE KEY `user_buku` (`id_user`,`id_buku`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_buku` (`id_buku`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id_peminjaman`),
  ADD KEY `id_buku` (`id_buku`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`id_setting`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `buku`
--
ALTER TABLE `buku`
  MODIFY `id_buku` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `favorit`
--
ALTER TABLE `favorit`
  MODIFY `id_favorit` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id_peminjaman` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `setting`
--
ALTER TABLE `setting`
  MODIFY `id_setting` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `buku`
--
ALTER TABLE `buku`
  ADD CONSTRAINT `buku_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`);

--
-- Constraints for table `favorit`
--
ALTER TABLE `favorit`
  ADD CONSTRAINT `fk_favorit_buku` FOREIGN KEY (`id_buku`) REFERENCES `buku` (`id_buku`),
  ADD CONSTRAINT `fk_favorit_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Constraints for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`id_buku`) REFERENCES `buku` (`id_buku`),
  ADD CONSTRAINT `peminjaman_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
