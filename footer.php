</div>
<script src="jquery-3.7.1.min.js"></script>
<!-- <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script> -->
<script src="DataTables/datatables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="bootstrap/js/bootstrap.js"></script>
<script src="script.js"></script>
<script src="js/app.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.2.0/exceljs.min.js"></script>
<script>
    

    function logout() {
        Swal.fire({
            title: "คุณต้องการออกจากระบบ?",
            text: "ต้องการออกจากระบบ ใช่หรือไม่?",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "ใช่",
            cancelButtonText: "ไม่ใช่"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "php_logout.php";
            }
        });
    }

    function addtocart() {
        var Data = new FormData();
        Data.append('equipment_Code', $("#equipment_Code").text());

        $.ajax({
            url: 'php_cart.php',
            type: 'POST',
            dataType: 'json',
            data: Data,
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.status === 'success') {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "เพิ่มลงตะกร้าแล้ว",
                        showConfirmButton: false,
                        timer: 1000
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        position: "center",
                        icon: "info",
                        title: res.status,
                        showConfirmButton: false,
                        timer: 1000
                    });
                }
            }
        });
    }
</script>

</body>

</html>