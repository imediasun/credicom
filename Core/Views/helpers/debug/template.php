<?php
    $data = cerTemplateDebug::$debug;
    $previousItemTimeEnd = null;
?>
<style>
    .templateDebug { border-collapse: collapse; width: 100%;}
    .templateDebug td, .templateDebug th { border: 1px solid #000000; padding: 3px}
    .templateDebug tr:hover td { background-color: #efefef; }
    .templateDebug .backtrace { display:none; }
</style>
<table class="templateDebug">
    <tr>
        <th style="width: 30px;">Delay</th>
        <th style="width: 30px;">Time</th>
        <th>Template</th>
    </tr>
    <?php foreach($data as $item) {?>
        <?php $delayTime = ($previousItemTimeEnd) ? $item['timeStart'] - $previousItemTimeEnd : null; ?>
        <tr>
            <td valign='top' align="center" style="white-space:nowrap;"><?= sprintf("%0.3f",$delayTime*1000) ?> ms</td>
            <td valign='top' align="center" style="white-space:nowrap;"><b><?= sprintf("%0.3f",$item['time']*1000) ?> ms</b></td>
            <td>
                <?= $item['template'] ?>
                <div class="backtrace" style=''><?= $item['backtrace'] ?></div>
            </td>
        </tr>
        <?php $previousItemTimeEnd = $item['timeEnd']; ?>
    <?php }?>
    <tr>
        <td></td>
        <td align="center">
            <?php 
                $timeSum = 0;
                foreach($data as $item) { $timeSum += $item['time']; }
            ?>
            <b><?= sprintf("%0.3f",$timeSum) ?> s</b>
        </td>
        <td align="left"><b>Total</b></td>
    </tr>
</table>
<script>
    $(document).ready(function() {
        $('.templateDebug td').unbind('dblclick').dblclick(function() {
            $(this).closest('tr').find('.backtrace').toggle();
        });
    });
</script>