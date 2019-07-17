<?php
$messages = isset($_SESSION['page_messages']) ? $_SESSION['page_messages'] : null;
if($messages) {
    foreach($messages as $message) {
        $class = 'message_'.$message['type'];
        echo "<span class='". $class ."'>" . $message['text'] . "</span><br>"; 
    }
    unset($_SESSION['page_messages']);
}