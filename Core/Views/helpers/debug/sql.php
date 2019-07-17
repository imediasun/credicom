<?php
    $data = cerDatabaseDebug::$debug;
    $dataCounter = cerDatabaseDebug::$debugCounter;
    $previousItemTimeEnd = null;
?>
<style>
    .sqlDebug { border-collapse: collapse; width: 100%;}
    .sqlDebug td, .sqlDebug th { border: 1px solid #000000; padding: 3px}
    .sqlDebug tr:hover td { background-color: #efefef; }
    .sqlDebug .backtrace { display:none; }
</style>
<table class="sqlDebug">
    <tr>
        <th style="width: 30px;">Delay</th>
        <th style="width: 30px;">Time</th>
        <th style="width: 10px;">Count</th>
        <th>SQL</th>
    </tr>
    <?php foreach($data as $item) {?>
        <?php $delayTime = ($previousItemTimeEnd) ? $item['timeStart'] - $previousItemTimeEnd : null; ?>
        <tr>
            <td valign='top' align="center" style="white-space:nowrap;"><?= sprintf("%0.3f",$delayTime*1000) ?> ms</td>
            <td valign='top' align="center" style="white-space:nowrap;"><b><?= sprintf("%0.3f",$item['time']*1000) ?> ms</b></td>
            <td valign='top' align="center"><b><i><?= (($dataCounter[$item['counterKey']] > 1) ? $dataCounter[$item['counterKey']] : '&nbsp;') ?></i></b></td>
            <td>
                <?= $item['query'] ?>
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
        <td align="left" colspan="2"><b>Total</b></td>
    </tr>
</table>
<script>
    $(document).ready(function() {
        $('.sqlDebug td').unbind('dblclick').dblclick(function(e) {
            $(this).closest('tr').find('.backtrace').toggle();
        });
    });
</script>
<br/>
<br/>
<?= cerTemplateDebug::renderDebug();?>