<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CalculateMoney</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5 ">
        <h1 class="text-center ">PHP Calculate Money</h1>
        <hr>
        <p class="text-center">กรุณากรอกข้อมูลเพื่อทำการคำนวณยอดเงิน</p>

        <form action="" method="post" class="text-center">
            <div class="row justify-content-center mb-3">
                <div class="form-group col-md-5">
                    <label for="price">Price</label>
                    <input type="number" name="price" id="price"
                        value="<?php echo isset($_POST['price']) ? $_POST['price'] : ''; ?>"
                        class="form-control w-100 mx-auto" placeholder="Enter a Price" required>
                </div>
                <div class="form-group col-md-5">
                    <label for="amount">Amount</label>
                    <input type="number" name="amount" id="amount"
                        value="<?php echo isset($_POST['amount']) ? $_POST['amount'] : ''; ?>"
                        class="form-control w-100 mx-auto" placeholder="Enter a Amount" required>
                </div>
            </div>
            <div>
                <div class="">
                    <label class="form-label d-block" for="">membership ?</label>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="member" id="member1" value="1"
                        <?php
                            echo isset($_POST['member'] )&& $_POST['member'] =='1'?'checked': '';
                        ?> 
                        >
                        <label for="member">Member (10% discount)</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="member" id="member2" value="0"
                        <?php
                            echo isset($_POST['member'] )&& $_POST['member'] =='0'?'checked': '';
                        ?> 
                        >
                        <label for="member">Not a Member </label>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-lg mt-3 mb-3 ">Calculate</button>
            <button type="button" class="btn btn-secondary btn-lg mt-3 mb-3 " onclick="clearAlldata()">Reset All</button>
        </form>
            <div class="card bg-info text-white text-center h5 py-3">
                <h5 class="mb-0">Show Result</h5> 
            </div>
            <div class="card py-1" id="Result">
                <?php 
                    if(isset($_POST['price'])&&isset($_POST['amount'])){
                        $price = $_POST['price'];
                        $amount = $_POST['amount'];
                        if(is_numeric($price)&& is_numeric($amount)){
                            $price = floatval($price);
                            $amount = floatval($amount);
                            $total = $price*$amount; // คำนวณยอดรวม
                            $discount = $total * 0.1 ;
                            
                            if(isset($_POST['member'])&&$_POST['member'] =='1'){
                                $total_paid = $total-$discount;
                                echo "<ul class='list-group list-group-flush'>";
                                echo "<li class='list-group-item'>ราคาสินค้า: <strong>" . number_format($price,2) . "</strong></li>";
                                echo "<li class='list-group-item'>จำนวนสินค้า: <strong>" . number_format($amount,2) . "</strong></li>";
                                echo "<li class='list-group-item'>ยอดซื้อรวม: <strong>" . number_format($total,2) . "</strong></li>";
                                echo "<li class='list-group-item'>ส่วนลดที่ได้: <strong>" . number_format($discount,2) . "</strong></li>";
                                echo "<li class='list-group-item text-primary'>ยอดที่ต้องจ่ายจริงหลังหักส่วนลด: <strong>" . number_format($total_paid,2) . "</strong></li>";
                                echo "</ul>";
                                
                            }else{
                                $total_paid = $total;
                                echo "<ul class='list-group list-group-flush'>";
                                echo "<li class='list-group-item'>ราคาสินค้า: <strong>" . number_format($price,2) . "</strong></li>";
                                echo "<li class='list-group-item'>จำนวนสินค้า: <strong>" . number_format($amount,2) . "</strong></li>";
                                echo "<li class='list-group-item'>ยอดซื้อรวม: <strong>" . number_format($total,2) . "</strong></li>";
                                echo "<li class='list-group-item text-primary'>ยอดที่ต้องจ่ายจริงหลังหักส่วนลด: <strong>" . number_format($total_paid,2) . "</strong></li>";
                                echo "</ul>";
                            }


                        }else{
                            echo "<div class='alert alert-danger text-center'>Please input Price and Amount.</div>";
                        }


                        }else{
                            echo "<div class='alert alert-secendary text-center'>Please input valid numeric value for Price and Amount.</div>";
                        }
                ?>
            </div>
         
        <hr>
    </div>
    <a href="index.php">Home</a>
    <script>
        // ฟังก์ชันสำหรับล้างผลลัพธ์เกรดและช่องกรอกคะแนน
        function clearAlldata() {
            document.getElementById('Result').innerHTML = '';
            document.getElementById('price').value = '';
            document.getElementById('member1').checked = false;
            document.getElementById('member2').checked = true;
            document.getElementById('amount').value = '';
        }  
    </script>
</body>

</html>