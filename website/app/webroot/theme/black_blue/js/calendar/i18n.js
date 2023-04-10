import { hf } from './helpers'

/**
 * Dictionary defaults
 */
export const DICT_DEFAULTS = {
    btnOk: 'OK',
    btnCancel: 'CANCEL',
    btnClear: 'CLEAR'
}

class Locale {
    /**
     * Creates i18n locale
     * @param {string[]} months List of month names
     * @param {string[]} shortMonths List of shortened month names
     * @param {string[]} days List of day names
     * @param {string[]} shortDays List of 3-letter day names
     * @param {string[]} shorterDays List of 2-letter day names
     * @param {number} firstDay First day of the week (1 - 7; Monday - Sunday)
     * @param {Object} dict Dictionary of words to be used on the UI
     * @param {string} dict.btnOk OK button text
     * @param {string} dict.btnCancel Cancel button text
     * @param {string} dict.btnClear Clear button text
     */
    constructor(months, shortMonths, days, shortDays, shorterDays, firstDay, dict) {
        this.months = months
        this.shortMonths = shortMonths || this.months.map(x => x.substr(0, 3))
        this.days = days
        this.shortDays = shortDays || this.days.map(x => x.substr(0, 3))
        this.shorterDays = shorterDays || this.days.map(x => x.substr(0, 2))
        this.firstDay = firstDay
        this.dict = hf.extend(DICT_DEFAULTS, dict)
    }
}

/**
 * Internationalization
 */
export const i18n = {
    // expose Locale class
    Locale: Locale,
    /**
     * English
     */
    en: new Locale('January_February_March_April_May_June_July_August_September_October_November_December'.split('_'), null,
        'Sunday_Monday_Tuesday_Wednesday_Thursday_Friday_Saturday'.split('_'), null, null, 7),
    /**
     * Russian
     */
    ru: new Locale('??????_???????_????_??????_???_????_????_??????_????????_???????_??????_???????'.split('_'), 
        '???._????._???._???._???_????_????_???._????._???._????._???.'.split('_'), 
        '???????????_???????????_???????_?????_???????_???????_???????'.split('_'), 
        '??_??_??_??_??_??_??'.split('_'), 
        '??_??_??_??_??_??_??'.split('_'), 1, {
            btnCancel: '????????', btnClear: '????????'
        }),
    /**
     * Spanish
     */
    es: new Locale('enero_febrero_marzo_abril_mayo_junio_julio_agosto_septiembre_octubre_noviembre_diciembre'.split('_'), null, 
        'domingo_lunes_martes_mi�rcoles_jueves_viernes_s�bado'.split('_'), 
        'dom._lun._mar._mi�._jue._vie._s�b.'.split('_'), null, 1, {
            btnCancel: 'Cancelar', btnClear: 'Vaciar'
        }),
    /**
     * Turkish
     */
    tr: new Locale('Ocak_?ubat_Mart_Nisan_May?s_Haziran_Temmuz_A?ustos_Eyl�l_Ekim_Kas?m_Aral?k'.split('_'), null, 
        'Pazar_Pazartesi_Sal?_�ar?amba_Per?embe_Cuma_Cumartesi'.split('_'), 
        'Paz_Pts_Sal_�ar_Per_Cum_Cts'.split('_'), 
        'Pz_Pt_Sa_�a_Pe_Cu_Ct'.split('_'), 1),
    /**
     * Persian
     */
    fa: new Locale('??????_?????_????_?????_??_????_?????_???_???????_?????_??????_??????'.split('_'), 
        '??????_?????_????_?????_??_????_?????_???_???????_?????_??????_??????'.split('_'), 
        '??\u200c????_??????_??\u200c????_????????_???\u200c????_????_????'.split('_'), 
        '??\u200c????_??????_??\u200c????_????????_???\u200c????_????_????'.split('_'), 
        '?_?_?_?_?_?_?'.split('_'), 1),
    /**
     * French
     */
    fr: new Locale('janvier_f�vrier_mars_avril_mai_juin_juillet_ao�t_septembre_octobre_novembre_d�cembre'.split('_'), 
        'janv._f�vr._mars_avr._mai_juin_juil._ao�t_sept._oct._nov._d�c.'.split('_'),
        'dimanche_lundi_mardi_mercredi_jeudi_vendredi_samedi'.split('_'), 
        'dim._lun._mar._mer._jeu._ven._sam.'.split('_'), 
        'di_lu_ma_me_je_ve_sa'.split('_'), 1, {
            btnCancel: 'Abandonner', btnClear: 'Effacer'
        }),
    /**
     * German
     */
    de: new Locale('Januar_Februar_M�rz_April_Mai_Juni_Juli_August_September_Oktober_November_Dezember'.split('_'), 
        'Jan._Feb._M�rz_Apr._Mai_Juni_Juli_Aug._Sep._Okt._Nov._Dez.'.split('_'),
        'Sonntag_Montag_Dienstag_Mittwoch_Donnerstag_Freitag_Samstag'.split('_'), 
        'So._Mo._Di._Mi._Do._Fr._Sa.'.split('_'), 
        'So_Mo_Di_Mi_Do_Fr_Sa'.split('_'), 1, {
            btnCancel: 'Stornieren', btnClear: 'L�schen'
        }),
    /**
     * Japanese
     */
    ja: new Locale('??_??_??_??_??_??_??_??_??_??_???_???'.split('_'), 
        '1?_2?_3?_4?_5?_6?_7?_8?_9?_10?_11?_12?'.split('_'),
        '???_???_???_???_???_???_???'.split('_'), 
        '??_??_??_??_??_??_??'.split('_'), 
        '?_?_?_?_?_?_?'.split('_'), 7),
    /**
     * Portuguese
     */
    pt: new Locale('janeiro_fevereiro_mar�o_abril_maio_junho_julho_agosto_setembro_outubro_novembro_dezembro'.split('_'), null,
        'Domingo_Segunda-feira_Ter�a-feira_Quarta-feira_Quinta-feira_Sexta-feira_S�bado'.split('_'), 
        'Dom_Seg_Ter_Qua_Qui_Sex_S�b'.split('_'), 
        'Do_2�_3�_4�_5�_6�_S�'.split('_'), 1, {
            btnCancel: 'Cancelar', btnClear: 'Clarear'
        }),
    /**
     * Vietnamese
     */
    vi: new Locale('th�ng 1_th�ng 2_th�ng 3_th�ng 4_th�ng 5_th�ng 6_th�ng 7_th�ng 8_th�ng 9_th�ng 10_th�ng 11_th�ng 12'.split('_'), 
        'Thg 01_Thg 02_Thg 03_Thg 04_Thg 05_Thg 06_Thg 07_Thg 08_Thg 09_Thg 10_Thg 11_Thg 12'.split('_'), 
        'ch? nh?t_th? hai_th? ba_th? t?_th? n?m_th? s�u_th? b?y'.split('_'), 
        'CN_T2_T3_T4_T5_T6_T7'.split('_'), 
        'CN_T2_T3_T4_T5_T6_T7'.split('_'), 1),
    /**
     * Chinese
     */
    zh: new Locale('??_??_??_??_??_??_??_??_??_??_???_???'.split('_'),
        '1?_2?_3?_4?_5?_6?_7?_8?_9?_10?_11?_12?'.split('_'),
        '???_???_???_???_???_???_???'.split('_'),
        '??_??_??_??_??_??_??'.split('_'),
        '?_?_?_?_?_?_?'.split('_'), 1)
}