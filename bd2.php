<?php
$pdo = new PDO("mysql:host=localhost;dbname=meBase", "root", "");
$pdo->query("SET NAMES UTF8");


//функция для удаления данных
function del($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM tasks  WHERE id =:id LIMIT 1");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
}

if (isset($_GET['del_id'])) {//проверяем, есть ли переменная,если есть удаляем
    del($pdo, $_GET['del_id']);
header("Location: bd2.php");
}

if (isset($_GET['red_id']) && isset($_POST['description'])) { //Проверяем, передана ли переменная на редактирования
         //Если новое имя предано, то обновляем
        updateTask($pdo, $_GET['red_id'], $_POST['description']);    
header("Location: bd2.php");
        }
        
        function updateTask($pdo, $id, $description) {
    $stmt = $pdo->prepare("UPDATE tasks set description = :description WHERE id=:id LIMIT 1 ");
$stmt->bindParam(':description', $description);
$stmt->bindParam(':id', $id);
$stmt->execute();
}
    
  //функция для добавления данных  
    function add($pdo, $id, $description) {
    $stmt = $pdo->prepare("INSERT INTO tasks (description, date_added) VALUES (:description, :date_added)");
$date_added= date("Y-m-d H:i:s");
$stmt->bindParam(':description', $description);
$stmt->bindParam(':date_added', $date_added);
$stmt->execute();
}    
    
    
//добавляем данные после проверки
if (isset($_POST["save"]) and ($_POST['description'])!=''){

add($pdo, $id, $_POST["description"]);  
header("Location: bd2.php");
}

//достаем данные
$sql = "SELECT id, description, is_done, date_added FROM tasks";
echo '<table cellpadding="5" cellspacing="0" border="1">';
echo "<tr>";
foreach ($pdo->query($sql) as $row) {
   
    echo "<td>".$row['id']."</td>";
    echo "<td>".$row['description']."</td>";
    echo "<td>".$row['is_done']."</td>";
    echo "<td>".$row['date_added']."</td>";
    echo '<td><a href="?del_id='.$row['id'].'">Удалить</a>'
            . ' <a href="?red_id='.$row['id'].'">Изменить</a></td>';
     echo "</tr>";  
}
echo "</table>";

if (isset($_GET['red_id'])) { //Если передана переменная на редактирование
        //Достаем запсись из БД
    $c = $_GET['red_id'];
    $stmt = $pdo->query("SELECT id, description, is_done, date_added FROM tasks WHERE id =$c"); //запрос к БД
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
       //print_r($result);
?>
<form method="POST">
        
        <input type="text" name="description" placeholder="Описание задачи" value="<?php echo $result['description']; ?>" />
        <input type="submit" name="sand" value="Изменить" />
    </form>
<?php
}
?>

<div style="float: left">
    <form method="POST">
        <input type="text" name="description" placeholder="Описание задачи" value="" />
        <input type="submit" name="save" value="Добавить" />
    </form>
</div>

