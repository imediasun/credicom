<?php
namespace App\modules\CreditRequest\Model;

use \App\modules\Core\Model\Base as BaseModel;


class CreditRequest extends BaseModel {

    const STATUS_NOT_INSTALLED = 0;
    const STATUS_OFFEN = 1;
    const STATUS_WDV_PFS = 19;
    const STATUS_WDV_KLARUNG = 20;
    const STATUS_WDV_KLARUNG_VEB = 38;
    const STATUS_WDV_SK = 39;
    const STATUS_WDV_MA = 76;
    const STATUS_KUNDENVERSAND_VEB = 23;
    const STATUS_KUNDENVERSAND_PFS_DSL = 24;
    const STATUS_KUNDENVERSAND_PFS_FEHLENDE_BV = 50;
    const STATUS_KUNDENVERSAND_FINANZCHECK = 52;
    const STATUS_KUNDENVERSAND_SK = 11;
    const STATUS_SALES_FORCES = 25;
    const STATUS_FEHLENDE_UNTERLAGEN_CREDICOM = 4;
    const STATUS_BANKVERSAND_VEB = 26;
    const STATUS_BANKVERSAND_PFS = 27;
    const STATUS_BANKVERSAND_SK = 56;
	const STATUS_BANKVERSAND_DSL = 81;
    const STATUS_FEHLENDE_UNTERLAGEN_BANK = 5;
    const STATUS_AUSGEZAHLT_VEB = 28;
    const STATUS_AUSGEZAHLT_FINANZCHECK = 40;
    const STATUS_AUSGEZAHLT_PFS_B2C = 41;
    const STATUS_AUSGEZAHLT_SK = 37;
    const STATUS_AUSGEZAHLT_DSL = 67;
    const STATUS_VEB_IMMO = 57;
    const STATUS_VEB_IMMO_SA_VERSENDET = 71;
    const STATUS_VEB_IMMO_FEHLENDE_UNTERLAGEN = 72;
    const STATUS_VEB_IMMO_AUSGEZAHLT = 73;
	 const STATUS_VEB_IMMO_BANKVERSAND = 82;
	
    const STATUS_WBC = 59;
    const STATUS_WBC_AUSGEZAHLT = 61;
    const STATUS_WBC_DSL_AUSGEZAHLT = 68;
    const STATUS_WBC_DSL_VERSANDT = 69;
    const STATUS_ABGELEHNT = 6;
    const STATUS_NV_SV = 10;
    const STATUS_NV_MB = 13;
    const STATUS_NV_AUSGELASTET = 43;
    const STATUS_NV_NEGATIVE_SCHUFA = 58;
    const STATUS_NV_KEIN_KONTAKT = 16;
    const STATUS_NV_SELBSTANDIG = 14;
    const STATUS_NV_SONSTIGE = 12;
    const STATUS_NV_AUSLANDISCHER_WS = 36;
    const STATUS_NV_AUSLANDISCHER_AG = 54;
    const STATUS_WIDERRUF = 8;
    const STATUS_WIDERRUF_LOSCHUNG_DER_DATEN = 30;
    const STATUS_DOPPLER = 31;
    const STATUS_STORNO = 45;
    const STATUS_FEHLENDE_UNTERLAGEN_SK_CREDICOM = 62;
    const STATUS_FEHLENDE_UNTERLAGEN_SK_BANK = 63;
    const STATUS_ABGELEHNT_SK = 64;
    const STATUS_AUXMONEY = 70;
    const STATUS_AUXMONEY_VERTRAG = 74;
    const STATUS_AUXMONEY_AUSGEZAHLT = 75;
    const STATUS_FINANZCHECK_TIPPGEBER = 46;    
    const STATUS_CREDIT12_DE = 78;
	const STATUS_WDV_SKMA = 79;
	const STATUS_NICHT_VERMITTELBAR = 80;
    

    public static function getStatusForSelect()
    {
        return [
            self::STATUS_NOT_INSTALLED=>'Not Installed',
            self::STATUS_OFFEN => "Offen",
            self::STATUS_WDV_PFS => "Wdv - PFS",
            self::STATUS_WDV_KLARUNG => "Wdv - Kl&auml;rung",//ä
            self::STATUS_WDV_KLARUNG_VEB => "Wdv - Klärung VEB",
            self::STATUS_WDV_SK => "Wdv - SK",
            self::STATUS_WDV_MA => "Wdv - MA",
            self::STATUS_KUNDENVERSAND_VEB => "Kundenversand VEB",
            self::STATUS_KUNDENVERSAND_PFS_DSL => "Kundenversand PFS - DSL",
            self::STATUS_KUNDENVERSAND_PFS_FEHLENDE_BV => "Kundenversand PFS fehlende BV",
            self::STATUS_KUNDENVERSAND_FINANZCHECK => "Kundenversand Finanzcheck",
            self::STATUS_KUNDENVERSAND_SK => "Kundenversand Sigma",
            self::STATUS_SALES_FORCES => "Sales Forces",
            self::STATUS_FEHLENDE_UNTERLAGEN_CREDICOM => "Fehlende Unterlagen credicom",
            self::STATUS_BANKVERSAND_VEB => "Bankversand VEB",
            self::STATUS_BANKVERSAND_PFS => "Bankversand PFS",
            self::STATUS_BANKVERSAND_SK => "Bankversand SK",
			self::STATUS_BANKVERSAND_DSL => "Bankversand DSL",
            self::STATUS_FEHLENDE_UNTERLAGEN_BANK => "Fehlende Unterlagen Bank",
            self::STATUS_AUSGEZAHLT_VEB => "Ausgezahlt - VEB",
            self::STATUS_AUSGEZAHLT_FINANZCHECK => "Ausgezahlt - Finanzcheck",
            self::STATUS_AUSGEZAHLT_PFS_B2C => "Ausgezahlt - PFS b2c",
            self::STATUS_AUSGEZAHLT_SK => "Ausgezahlt - Sigma",
            self::STATUS_AUSGEZAHLT_DSL => "Ausgezahlt - DSL",
            self::STATUS_VEB_IMMO => "VEB-immo",
            self::STATUS_VEB_IMMO_SA_VERSENDET => "VEB-Immo SA versendet",
            self::STATUS_VEB_IMMO_FEHLENDE_UNTERLAGEN => "VEB-Immo fehlende Unterlagen",
            self::STATUS_VEB_IMMO_AUSGEZAHLT => "VEB-Immo Ausgezahlt",
			self::STATUS_VEB_IMMO_BANKVERSAND => "VEB Immo Bankversand",			
            self::STATUS_WBC => "WBC",
            self::STATUS_WBC_AUSGEZAHLT => "WBC Ausgezahlt",
            self::STATUS_WBC_DSL_AUSGEZAHLT => "WBC DSL Ausgezahlt",
            self::STATUS_WBC_DSL_VERSANDT => "WBC DSL Versandt",
            self::STATUS_ABGELEHNT => "Abgelehnt",
            self::STATUS_NV_SV => "N.V. - SV",
            self::STATUS_NV_MB => "N.V. - MB",
            self::STATUS_NV_AUSGELASTET => "N.V. - ausgelastet",
            self::STATUS_NV_NEGATIVE_SCHUFA => "N.V. - negative Schufa",
            self::STATUS_NV_KEIN_KONTAKT => "N.V. - Kein Kontakt",
            self::STATUS_NV_SELBSTANDIG => "N.V. - Selbständig",
            self::STATUS_NV_SONSTIGE => "N.V. - sonstige",
            self::STATUS_NV_AUSLANDISCHER_WS => "N.V. - ausländischer WS",
            self::STATUS_NV_AUSLANDISCHER_AG => "N.V. Ausländischer AG",
            self::STATUS_WIDERRUF => "Widerruf",
            self::STATUS_WIDERRUF_LOSCHUNG_DER_DATEN => "Widerruf - Löschung der Daten",
            self::STATUS_DOPPLER => "Doppler",
            self::STATUS_STORNO => "STORNO",
            self::STATUS_FEHLENDE_UNTERLAGEN_SK_CREDICOM => "Fehlende Unterlagen SK - credicom",
            self::STATUS_FEHLENDE_UNTERLAGEN_SK_BANK => "Fehlende Unterlagen SK - Bank",
            self::STATUS_ABGELEHNT_SK => "Abgelehnt SK",
            self::STATUS_AUXMONEY => "Auxmoney",
            self::STATUS_AUXMONEY_VERTRAG => "Auxmoney Vertrag",
            self::STATUS_AUXMONEY_AUSGEZAHLT => "Auxmoney Ausgezahlt",            
            self::STATUS_FINANZCHECK_TIPPGEBER => "Finanzcheck Tippgeber",
            self::STATUS_CREDIT12_DE => "credit12.de",
			self::STATUS_WDV_SKMA => "Wdv - SKMA",
			self::STATUS_NICHT_VERMITTELBAR=>'nicht vermittelbar'
        ];
    }

}