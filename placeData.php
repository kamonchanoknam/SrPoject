<?php  
include("db_connect.php"); // เรียกใช้ไฟล์ ตั้งค่า และฟังก์ชั่น เกี่ยวกับฐานข้อมูล  
$mysqli = connect(); // สร้าง ตัวแปร mysql instance สำหรับเรียกใช้งานฐานข้อมูล  
// ส่วนแรก คือสำหรับแสดงผลข้อมูล  
header("Content-type:application/json; charset=UTF-8");          
header("Cache-Control: no-store, no-cache, must-revalidate");         
header("Cache-Control: post-check=0, pre-check=0", false); 
 
$sql="SELECT * FROM place_temp WHERE 1  ";  
$result = $mysqli->query($sql); 

while($rs=$result->fetch_object()){  
    $json_data[]=array(  
        "id"=>$rs->place_id,  
        "title"=>$rs->place_title,  
        "latitude"=>$rs->place_lat,  
        "longitude"=>$rs->place_lon  
    );    
}  
 
$json= json_encode($json_data);  
if(isset($_GET['callback']) && $_GET['callback']!=""){  
echo $_GET['callback']."(".$json.");";      
}else{  
echo $json;  
}      
exit;  
?>