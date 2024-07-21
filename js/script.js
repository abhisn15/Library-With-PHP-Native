// var keyword = document.getElementById('keyword');
// var tombolCari = document.getElementById('tombolCari');
// var container = document.getElementById('container');

// keyword.addEventListener('keyup', function () {
//   var xhr = new XMLHttpRequest();

//   xhr.onreadystatechange = function () {
//     if (xhr.readyState == 4 && xhr.status == 200) {
//       container.innerHTML = xhr.responseText;
//     }
//   }

//   xhr.open('GET', 'ajax/siswa.php?keyword=' + keyword.value, true);
//   xhr.send();
// })

$(document).ready(function () {
	// hilangkan tombol cari
	$("#tombolCari").hide();

	// event ketika keyword ditulis
	$("#keyword").on("keyup", function () {
		// munculkan icon loading
		$(".loader").show();

		// ajax menggunakan load
		// $('#container').load('ajax/siswa.php?keyword=' + $('#keyword').val());

		// $.get()
		$.get("../ajax/siswa.php?keyword=" + $("#keyword").val(), function (data) {
			$("#container").html(data);
			$(".loader").hide();
		});
	});
});