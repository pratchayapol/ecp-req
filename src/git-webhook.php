<?php
// บันทึก webhook log
file_put_contents("/tmp/webhook.log", date("Y-m-d H:i:s") . " - Webhook called\n", FILE_APPEND);

// ไปที่โฟลเดอร์โปรเจกต์แล้ว pull
exec('cd /home/ecp/version-php/ecp-req && git pull origin main >> /tmp/git-auto-pull.log 2>&1');
?>
