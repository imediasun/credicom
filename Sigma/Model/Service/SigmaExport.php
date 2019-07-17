<?php
 
namespace App\modules\Sigma\Model\Service;

use \League\Csv\Writer as CsvWriter;
use \App\SigmaOrder;
use \App\CreditOrder;
use \App\ArchiveSigmaExport;

class SigmaExportSearch
{
    public function findCreditOrder($credit_order_id)
    {
        $credit_order = CreditOrder::find($credit_order_id);
        return $credit_order;
    }
}

class SigmaExport
{

    public static function process()
    {
        $file_name = 'sigma_export_' . date('YmdHis') . '_' . rand(100, 999) . '.csv';
        $folder = sprintf('%s/files/cache/sigma/archive/export', base_path());
        $file = sprintf('%s/%s', $folder, $file_name);

        //get data/**/
        $data = self::getExportData();
        if (!count($data)) {
            //TODO: log [no data to export, return]
            return false;
        }

        //save data
        $fp = fopen($file, "a"); // Открываем файл в режиме записи
        $records = '';
        $count_records = 0;
        foreach ($data as $item) {
            $count_records++;
            $record = $item['nachname'] . ';' . $item['vorname'] . ';' . $item['geb_dat'] . ';' . $item['id'];
            if ($count_records < count($data)) {
                $record .= "\r\n";
            }
            $records .= $record;
            //fwrite($fp, $record);
        }
//dd(iconv_get_encoding('all'));

//$records = mb_convert_encoding($records, 'Windows-1252');

        $records = iconv(mb_detect_encoding($records), 'Windows-1252//TRANSLIT', $records);
        fputs($fp, $records);

        fclose($fp); //Закрытие файла

        $archive = new ArchiveSigmaExport;

        $archive->filename = $file_name;
        $archive->count_records = count($data);
        $archive->save();

        SigmaOrder::whereIn('id', array_column($data, 'id'))->update(['archive_sigma_export_id' => $archive->id, 'date_export' => date("Y-m-d H:i:s", time())]);

        //TODO send mail


        return $file;
    }


    public static function getExportData()
    {
        $sigma_orders = SigmaOrder::where('date_export', null)->with('credit_order')->get();
		
		//$sigma_orders = SigmaOrder::where('id','>','803')->where('id','<','825')->with('credit_order')->get();
		
        $result = [];
        foreach ($sigma_orders as $item) {
            if (null != $item->credit_order) {
                $id = $item->id;
                $nachname = $item->credit_order_type == 0 ? $item->credit_order->nachname : $item->credit_order->nachname1;
                $vorname = $item->credit_order_type == 0 ? $item->credit_order->vorname : $item->credit_order->vorname1;
                $gebdat = $item->credit_order_type == 0 ? $item->credit_order->gebdat : $item->credit_order->gebdat1;

                $exportItem = [];
                $exportItem['nachname'] = iconv('UTF-8', "iso-8859-1", utf8_encode(trim($nachname)));
                $exportItem['vorname'] = iconv('UTF-8', "iso-8859-1", utf8_encode(trim($vorname)));
                $exportItem['geb_dat'] = ($gebdat != '0000-00-00') ? date("d.m.Y", strtotime($gebdat)) : null;
                $exportItem['id'] = iconv('UTF-8', "iso-8859-1", utf8_encode(trim($id)));

                $result[] = $exportItem;
            }
        }

        return $result;
    }


}