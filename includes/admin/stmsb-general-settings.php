<?php

class STMSB_General_Settings
{
    function __construct()
    {
        add_action('admin_menu', array($this, 'stmsb_add_submenu_to_page'));
        add_action('admin_init', array($this, 'stmsb_register_session_booking_settings'));
    }

    public function stmsb_add_submenu_to_page()
    {
        add_submenu_page(
            _x('sm-session-management', 'stmsb_session_booking'),
            _x('Session Management', 'stmsb_session_booking'),
            'General Settings',
            'manage_options',
            'general_settings',
            array($this, 'stmsb_general_settings')
        );
    }

    function stmsb_general_settings()
    {
        ?>
        <div class="wrap">
            <?php include_once(dirname(__FILE__) . '/stmsb-header.php') ?>
            <h4><strong>General Settings</strong></h4>
            <p><?php esc_html_e('Changes you settings here', 'stmsb_session_booking') ?></p>
            <!--suppress HtmlUnknownTarget -->
            <form method="post" action="options.php" id="stmsb_option_form">
                <?php settings_fields('stmsb_session_booking_settings'); ?>
                <?php do_settings_sections('stmsb_session_booking_settings'); ?>
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row"><label for="stmsb_booking_timezone">Timezone</label></th>
                        <td>
                            <?php
                            $africas = $this->get_time_zone_africa();
                            $americas = $this->get_time_zone_america();
                            $antarcticas = $this->get_time_zone_antarctica();
                            $arctics = $this->get_time_zone_arctic();
                            $asias = $this->get_time_zone_asia();
                            $atlantics = $this->get_time_zone_atlantic();
                            $australias = $this->get_time_zone_australia();
                            $europs = $this->get_time_zone_europe();
                            $indians = $this->get_time_zone_indian();
                            $pacifics = $this->get_time_zone_pacific();
                            ?>
                            <select style="width: 30%;" id="stmsb_booking_timezone" name="stmsb_booking_timezone">
                                <option disabled selected hidden value="">------ Please select timezone ------</option>
                                <optgroup label="Africa">
                                    <?php
                                    foreach ($africas as $africa => $key) {
                                        $sSel = ($africa == get_option('stmsb_booking_timezone')) ? "Selected='selected'" : "";
                                        echo esc_html("<option  value=" . $africa . "  $sSel>$key</option>");
                                    }
                                    ?>
                                </optgroup>
                                <optgroup label="America">
                                    <?php
                                    foreach ($americas as $america => $key) {
                                        $sSel = ($america == get_option('stmsb_booking_timezone')) ? "Selected='selected'" : "";
                                        echo esc_html("<option  value=" . $america . "  $sSel>$key</option>");
                                    }
                                    ?>
                                </optgroup>
                                <optgroup label="Antarctica">
                                    <?php
                                    foreach ($antarcticas as $antarctica => $key) {
                                        $sSel = ($antarctica == get_option('stmsb_booking_timezone')) ? "Selected='selected'" : "";
                                        echo esc_html("<option  value=" . $antarctica . "  $sSel>$key</option>");
                                    }
                                    ?>
                                </optgroup>
                                <optgroup label="Arctic">
                                    <?php
                                    foreach ($arctics as $arctic => $key) {
                                        $sSel = ($arctic == get_option('stmsb_booking_timezone')) ? "Selected='selected'" : "";
                                        echo esc_html("<option  value=" . $arctic . "  $sSel>$key</option>");
                                    }
                                    ?>
                                </optgroup>
                                <optgroup label="Asia">
                                    <?php
                                    foreach ($asias as $asia => $key) {
                                        $sSel = ($asia == get_option('stmsb_booking_timezone')) ? "Selected='selected'" : "";
                                        echo esc_html("<option  value=" . $asia . "  $sSel>$key</option>");
                                    }
                                    ?>
                                </optgroup>
                                <optgroup label="Atlantic">
                                    <?php
                                    foreach ($atlantics as $atlantic => $key) {
                                        $sSel = ($atlantic == get_option('stmsb_booking_timezone')) ? "Selected='selected'" : "";
                                        echo esc_html("<option  value=" . $atlantic . "  $sSel>$key</option>");
                                    }
                                    ?>
                                </optgroup>
                                <optgroup label="Australia">
                                    <?php
                                    foreach ($australias as $australia => $key) {
                                        $sSel = ($australia == get_option('stmsb_booking_timezone')) ? "Selected='selected'" : "";
                                        echo esc_html("<option  value=" . $australia . "  $sSel>$key</option>");
                                    }
                                    ?>
                                </optgroup>
                                <optgroup label="Europe">
                                    <?php
                                    foreach ($europs as $europ => $key) {
                                        $sSel = ($europ == get_option('stmsb_booking_timezone')) ? "Selected='selected'" : "";
                                        echo esc_html("<option  value=" . $europ . "  $sSel>$key</option>");
                                    }
                                    ?>
                                </optgroup>
                                <optgroup label="Indian">
                                    <?php
                                    foreach ($indians as $indian => $key) {
                                        $sSel = ($indian == get_option('stmsb_booking_timezone')) ? "Selected='selected'" : "";
                                        echo esc_html("<option  value=" . $indian . "  $sSel>$key</option>");
                                    }
                                    ?>
                                </optgroup>
                                <optgroup label="Pacific">
                                    <?php
                                    foreach ($pacifics as $pacific => $key) {
                                        $sSel = ($pacific == get_option('stmsb_booking_timezone')) ? "Selected='selected'" : "";
                                        echo esc_html("<option  value=" . $pacific . "  $sSel>$key</option>");
                                    }
                                    ?>
                                </optgroup>
                            </select>
                            <p class="description" id="stmsb_booking_timezone">Select you own timezone for correct
                                session dates and time.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="stmsb_display_date_format">Date Format</label></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><span>Date Format</span></legend>
                                <label><input type="radio" name="stmsb_display_date_format" value="d F Y"
                                              <?php if (get_option('stmsb_display_date_format') == ''): echo esc_html("checked"); endif;
                                              if (get_option('stmsb_display_date_format') == 'd F Y'): ?>checked<?php endif; ?> >
                                    <span
                                            class="date-time-text format-i18n">12 July 2018</span></label><br>
                                <label><input type="radio" name="stmsb_display_date_format" value="Y-m-d"
                                              <?php if (get_option('stmsb_display_date_format') == 'Y-m-d'): ?>checked<?php endif; ?> >
                                    <span
                                            class="date-time-text format-i18n">2018-07-12</span></label><br>
                                <label><input type="radio" name="stmsb_display_date_format" value="m/d/Y"
                                              <?php if (get_option('stmsb_display_date_format') == 'm/d/Y'): ?>checked<?php endif; ?> >
                                    <span
                                            class="date-time-text format-i18n">07/12/2018</span></label><br>
                                <label><input type="radio" name="stmsb_display_date_format" value="d/m/Y"
                                              <?php if (get_option('stmsb_display_date_format') == 'd/m/Y'): ?>checked<?php endif; ?> >
                                    <span
                                            class="date-time-text format-i18n">12/07/2018</span></label><br>
                            </fieldset>
                            <p class="description" id="stmsb_display_date_format">Select how you want to display
                                date.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="stmsb_display_time_format">Time Format</label></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><span>Time Format</span></legend>
                                <label><input type="radio" name="stmsb_display_time_format" value="g:i A"
                                              <?php if (get_option('stmsb_display_time_format') == ''): echo esc_html("checked"); endif;
                                              if (get_option('stmsb_display_time_format') == 'g:i A'): ?>checked<?php endif; ?>>
                                    <span
                                            class="date-time-text format-i18n">10:40 AM</span></label><br>
                                <label><input type="radio" name="stmsb_display_time_format" value="g:i a"
                                              <?php if (get_option('stmsb_display_time_format') == 'g:i a'): ?>checked<?php endif; ?>>
                                    <span
                                            class="date-time-text format-i18n">10:40 am</span></label><br>
                                <label><input type="radio" name="stmsb_display_time_format" value="H:i"
                                              <?php if (get_option('stmsb_display_time_format') == 'H:i'): ?>checked<?php endif; ?>>
                                    <span
                                            class="date-time-text format-i18n">10:40</span></label><br>
                            </fieldset>
                            <p class="description" id="stmsb_display_time_format">Select how you want to display
                                time.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="stmsb_display_rows">Number of record to display</label></th>
                        <td>
                            <input style="width: 30%" type="text" name="stmsb_display_rows" id="stmsb_display_rows"
                                   value="<?php if (get_option('stmsb_display_rows')) {
                                       echo esc_attr(get_option('stmsb_display_rows'));
                                   } else {
                                       echo "10";
                                   }
                                   ?>">
                            <p class="description" id="stmsb_display_rows">Type number of records to be displayed in
                                Session Slots, Manual Booking and Session Logs.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="stmsb_custom_css">Custom CSS:</label></th>
                        <td>
                        <textarea name="stmsb_custom_css" id="stmsb_custom_css"
                                  class="large-text code" rows="10" spellcheck="false">
                            <?php if (get_option('stmsb_custom_css')): ?>
                                <?php echo esc_attr(trim(get_option('stmsb_custom_css', ' ')));
                                ?>
                                <?php stmsb_write_custom_css(sanitize_text_field(get_option('stmsb_custom_css'))); ?>
                            <?php else: ?>
                                <?php echo esc_html(stmsb_read_custom_css()); ?>
                            <?php endif; ?>
                        </textarea>
                            <p class="description" id="stmsb_custom_css_description">Add your custom css at the end to
                                target on frontend or edit the following.</p>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>

        <?php
    }

    public function stmsb_register_session_booking_settings()
    {
        register_setting('stmsb_session_booking_settings', 'stmsb_booking_timezone');
        register_setting('stmsb_session_booking_settings', 'stmsb_display_date_format');
        register_setting('stmsb_session_booking_settings', 'stmsb_display_time_format');
        register_setting('stmsb_session_booking_settings', 'stmsb_display_rows');
        register_setting('stmsb_session_booking_settings', 'stmsb_custom_css');
    }

    public function get_time_zone_africa()
    {
        return array(
            "Africa/Abidjan" => "Abidjan",
            "Africa/Accra" => "Accra",
            "Africa/Addis_Ababa" => "Addis Ababa",
            "Africa/Algiers" => "Algiers",
            "Africa/Asmara" => "Asmara",
            "Africa/Bamako" => "Bamako",
            "Africa/Bangui" => "Bangui",
            "Africa/Banjul" => "Banjul",
            "Africa/Bissau" => "Bissau",
            "Africa/Blantyre" => "Blantyre",
            "Africa/Brazzaville" => "Brazzaville",
            "Africa/Bujumbura" => "Bujumbura",
            "Africa/Cairo" => "Cairo",
            "Africa/Casablanca" => "Casablanca",
            "Africa/Ceuta" => "Ceuta",
            "Africa/Conakry" => "Conakry",
            "Africa/Dakar" => "Dakar",
            "Africa/Dar_es_Salaam" => "Dar es Salaam",
            "Africa/Djibouti" => "Djibouti",
            "Africa/Douala" => "Douala",
            "Africa/El_Aaiun" => "El Aaiun",
            "Africa/Freetown" => "Freetown",
            "Africa/Gaborone" => "Gaborone",
            "Africa/Harare" => "Harare",
            "Africa/Johannesburg" => "Johannesburg",
            "Africa/Juba" => "Juba",
            "Africa/Kampala" => "Kampala",
            "Africa/Khartoum" => "Khartoum",
            "Africa/Kigali" => "Kigali",
            "Africa/Kinshasa" => "Kinshasa",
            "Africa/Lagos" => "Lagos",
            "Africa/Libreville" => "Libreville",
            "Africa/Lome" => "Lome",
            "Africa/Luanda" => "Luanda",
            "Africa/Lubumbashi" => "Lubumbashi",
            "Africa/Lusaka" => "Lusaka",
            "Africa/Malabo" => "Malabo",
            "Africa/Maputo" => "Maputo",
            "Africa/Maseru" => "Maseru",
            "Africa/Mbabane" => "Mbabane",
            "Africa/Mogadishu" => "Mogadishu",
            "Africa/Monrovia" => "Monrovia",
            "Africa/Nairobi" => "Nairobi",
            "Africa/Ndjamena" => "Ndjamena",
            "Africa/Niamey" => "Niamey",
            "Africa/Nouakchott" => "Nouakchott",
            "Africa/Ouagadougou" => "Ouagadougou",
            "Africa/Porto-Novo" => "Porto-Novo",
            "Africa/Sao_Tome" => "Sao Tome",
            "Africa/Tripoli" => "Tripoli",
            "Africa/Tunis" => "Tunis",
            "Africa/Windhoek" => "Windhoek"
        );
    }

    public function get_time_zone_america()
    {
        return array(
            "America/Adak" => "Adak",
            "America/Anchorage" => "Anchorage",
            "America/Anguilla" => "Anguilla",
            "America/Antigua" => "Antigua",
            "America/Araguaina" => "Araguaina",
            "America/Argentina/Buenos_Aires" => "Argentina - Buenos Aires",
            "America/Argentina/Catamarca" => "Argentina - Catamarca",
            "America/Argentina/Cordoba" => "Argentina - Cordoba",
            "America/Argentina/Jujuy" => "Argentina - Jujuy",
            "America/Argentina/La_Rioja" => "Argentina - La Rioja",
            "America/Argentina/Mendoza" => "Argentina - Mendoza",
            "America/Argentina/Rio_Gallegos" => "Argentina - Rio Gallegos",
            "America/Argentina/Salta" => "Argentina - Salta",
            "America/Argentina/San_Juan" => "Argentina - San Juan",
            "America/Argentina/San_Luis" => "Argentina - San Luis",
            "America/Argentina/Tucuman" => "Argentina - Tucuman",
            "America/Argentina/Ushuaia" => "Argentina - Ushuaia",
            "America/Aruba" => "Aruba",
            "America/Asuncion" => "Asuncion",
            "America/Atikokan" => "Atikokan",
            "America/Bahia" => "Bahia",
            "America/Bahia_Banderas" => "Bahia Banderas",
            "America/Barbados" => "Barbados",
            "America/Belem" => "Belem",
            "America/Belize" => "Belize",
            "America/Blanc-Sablon" => "Blanc-Sablon",
            "America/Boa_Vista" => "Boa Vista",
            "America/Bogota" => "Bogota",
            "America/Boise" => "Boise",
            "America/Cambridge_Bay" => "Cambridge Bay",
            "America/Campo_Grande" => "Campo Grande",
            "America/Cancun" => "Cancun",
            "America/Caracas" => "Caracas",
            "America/Cayenne" => "Cayenne",
            "America/Cayman" => "Cayman",
            "America/Chicago" => "Chicago",
            "America/Chihuahua" => "Chihuahua",
            "America/Costa_Rica" => "Costa Rica",
            "America/Creston" => "Creston",
            "America/Cuiaba" => "Cuiaba",
            "America/Curacao" => "Curacao",
            "America/Danmarkshavn" => "Danmarkshavn",
            "America/Dawson" => "Dawson",
            "America/Dawson_Creek" => "Dawson Creek",
            "America/Denver" => "Denver",
            "America/Detroit" => "Detroit",
            "America/Dominica" => "Dominica",
            "America/Edmonton" => "Edmonton",
            "America/Eirunepe" => "Eirunepe",
            "America/El_Salvador" => "El Salvador",
            "America/Fortaleza" => "Fortaleza",
            "America/Fort_Nelson" => "Fort Nelson",
            "America/Glace_Bay" => "Glace Bay",
            "America/Godthab" => "Godthab",
            "America/Goose_Bay" => "Goose Bay",
            "America/Grand_Turk" => "Grand Turk",
            "America/Grenada" => "Grenada",
            "America/Guadeloupe" => "Guadeloupe",
            "America/Guatemala" => "Guatemala",
            "America/Guayaquil" => "Guayaquil",
            "America/Guyana" => "Guyana",
            "America/Halifax" => "Halifax",
            "America/Havana" => "Havana",
            "America/Hermosillo" => "Hermosillo",
            "America/Indiana/Indianapolis" => "Indiana - Indianapolis",
            "America/Indiana/Knox" => "Indiana - Knox",
            "America/Indiana/Marengo" => "Indiana - Marengo",
            "America/Indiana/Petersburg" => "Indiana - Petersburg",
            "America/Indiana/Tell_City" => "Indiana - Tell City",
            "America/Indiana/Vevay" => "Indiana - Vevay",
            "America/Indiana/Vincennes" => "Indiana - Vincennes",
            "America/Indiana/Winamac" => "Indiana - Winamac",
            "America/Inuvik" => "Inuvik",
            "America/Iqaluit" => "Iqaluit",
            "America/Jamaica" => "Jamaica",
            "America/Juneau" => "Juneau",
            "America/Kentucky/Louisville" => "Kentucky - Louisville",
            "America/Kentucky/Monticello" => "Kentucky - Monticello",
            "America/Kralendijk" => "Kralendijk",
            "America/La_Paz" => "La Paz",
            "America/Lima" => "Lima",
            "America/Los_Angeles" => "Los Angeles",
            "America/Lower_Princes" => "Lower Princes",
            "America/Maceio" => "Maceio",
            "America/Managua" => "Managua",
            "America/Manaus" => "Manaus",
            "America/Marigot" => "Marigot",
            "America/Martinique" => "Martinique",
            "America/Matamoros" => "Matamoros",
            "America/Mazatlan" => "Mazatlan",
            "America/Menominee" => "Menominee",
            "America/Merida" => "Merida",
            "America/Metlakatla" => "Metlakatla",
            "America/Mexico_City" => "Mexico City",
            "America/Miquelon" => "Miquelon",
            "America/Moncton" => "Moncton",
            "America/Monterrey" => "Monterrey",
            "America/Montevideo" => "Montevideo",
            "America/Montserrat" => "Montserrat",
            "America/Nassau" => "Nassau",
            "America/New_York" => "New York",
            "America/Nipigon" => "Nipigon",
            "America/Nome" => "Nome",
            "America/Noronha" => "Noronha",
            "America/North_Dakota/Beulah" => "North Dakota - Beulah",
            "America/North_Dakota/Center" => "North Dakota - Center",
            "America/North_Dakota/New_Salem" => "North Dakota - New Salem",
            "America/Ojinaga" => "Ojinaga",
            "America/Panama" => "Panama",
            "America/Pangnirtung" => "Pangnirtung",
            "America/Paramaribo" => "Paramaribo",
            "America/Phoenix" => "Phoenix",
            "America/Port-au-Prince" => "Port-au-Prince",
            "America/Port_of_Spain" => "Port of Spain",
            "America/Porto_Velho" => "Porto Velho",
            "America/Puerto_Rico" => "Puerto Rico",
            "America/Punta_Arenas" => "Punta Arenas",
            "America/Rainy_River" => "Rainy River",
            "America/Rankin_Inlet" => "Rankin Inlet",
            "America/Recife" => "Recife",
            "America/Regina" => "Regina",
            "America/Resolute" => "Resolute",
            "America/Rio_Branco" => "Rio Branco",
            "America/Santarem" => "Santarem",
            "America/Santiago" => "Santiago",
            "America/Santo_Domingo" => "Santo Domingo",
            "America/Sao_Paulo" => "Sao Paulo",
            "America/Scoresbysund" => "Scoresbysund",
            "America/Sitka" => "Sitka",
            "America/St_Barthelemy" => "St Barthelemy",
            "America/St_Johns" => "St Johns",
            "America/St_Kitts" => "St Kitts",
            "America/St_Lucia" => "St Lucia",
            "America/St_Thomas" => "St Thomas",
            "America/St_Vincent" => "St Vincent",
            "America/Swift_Current" => "Swift Current",
            "America/Tegucigalpa" => "Tegucigalpa",
            "America/Thule" => "Thule",
            "America/Thunder_Bay" => "Thunder Bay",
            "America/Tijuana" => "Tijuana",
            "America/Toronto" => "Toronto",
            "America/Tortola" => "Tortola",
            "America/Vancouver" => "Vancouver",
            "America/Whitehorse" => "Whitehorse",
            "America/Winnipeg" => "Winnipeg",
            "America/Yakutat" => "Yakutat",
            "America/Yellowknife" => "Yellowknife"
        );
    }

    public function get_time_zone_antarctica()
    {
        return array(
            "Antarctica/Casey" => "Casey",
            "Antarctica/Davis" => "Davis",
            "Antarctica/DumontDUrville" => "DumontDUrville",
            "Antarctica/Macquarie" => "Macquarie",
            "Antarctica/Mawson" => "Mawson",
            "Antarctica/McMurdo" => "McMurdo",
            "Antarctica/Palmer" => "Palmer",
            "Antarctica/Rothera" => "Rothera",
            "Antarctica/Syowa" => "Syowa",
            "Antarctica/Troll" => "Troll",
            "Antarctica/Vostok" => "Vostok"
        );
    }

    public function get_time_zone_arctic()
    {
        return array(
            "Arctic/Longyearbyen" => "Longyearbyen"
        );
    }

    public function get_time_zone_asia()
    {
        return array(

            "Asia/Aden" => "Aden",
            "Asia/Almaty" => "Almaty",
            "Asia/Amman" => "Amman",
            "Asia/Anadyr" => "Anadyr",
            "Asia/Aqtau" => "Aqtau",
            "Asia/Aqtobe" => "Aqtobe",
            "Asia/Ashgabat" => "Ashgabat",
            "Asia/Atyrau" => "Atyrau",
            "Asia/Baghdad" => "Baghdad",
            "Asia/Bahrain" => "Bahrain",
            "Asia/Baku" => "Baku",
            "Asia/Bangkok" => "Bangkok",
            "Asia/Barnaul" => "Barnaul",
            "Asia/Beirut" => "Beirut",
            "Asia/Bishkek" => "Bishkek",
            "Asia/Brunei" => "Brunei",
            "Asia/Chita" => "Chita",
            "Asia/Choibalsan" => "Choibalsan",
            "Asia/Colombo" => "Colombo",
            "Asia/Damascus" => "Damascus",
            "Asia/Dhaka" => "Dhaka",
            "Asia/Dili" => "Dili",
            "Asia/Dubai" => "Dubai",
            "Asia/Dushanbe" => "Dushanbe",
            "Asia/Famagusta" => "Famagusta",
            "Asia/Gaza" => "Gaza",
            "Asia/Hebron" => "Hebron",
            "Asia/Ho_Chi_Minh" => "Ho Chi Minh",
            "Asia/Hong_Kong" => "Hong Kong",
            "Asia/Hovd" => "Hovd",
            "Asia/Irkutsk" => "Irkutsk",
            "Asia/Jakarta" => "Jakarta",
            "Asia/Jayapura" => "Jayapura",
            "Asia/Jerusalem" => "Jerusalem",
            "Asia/Kabul" => "Kabul",
            "Asia/Kamchatka" => "Kamchatka",
            "Asia/Karachi" => "Karachi",
            "Asia/Kathmandu" => "Kathmandu",
            "Asia/Khandyga" => "Khandyga",
            "Asia/Kolkata" => "Kolkata",
            "Asia/Krasnoyarsk" => "Krasnoyarsk",
            "Asia/Kuala_Lumpur" => "Kuala Lumpur",
            "Asia/Kuching" => "Kuching",
            "Asia/Kuwait" => "Kuwait",
            "Asia/Macau" => "Macau",
            "Asia/Magadan" => "Magadan",
            "Asia/Makassar" => "Makassar",
            "Asia/Manila" => "Manila",
            "Asia/Muscat" => "Muscat",
            "Asia/Nicosia" => "Nicosia",
            "Asia/Novokuznetsk" => "Novokuznetsk",
            "Asia/Novosibirsk" => "Novosibirsk",
            "Asia/Omsk" => "Omsk",
            "Asia/Oral" => "Oral",
            "Asia/Phnom_Penh" => "Phnom Penh",
            "Asia/Pontianak" => "Pontianak",
            "Asia/Pyongyang" => "Pyongyang",
            "Asia/Qatar" => "Qatar",
            "Asia/Qyzylorda" => "Qyzylorda",
            "Asia/Riyadh" => "Riyadh",
            "Asia/Sakhalin" => "Sakhalin",
            "Asia/Samarkand" => "Samarkand",
            "Asia/Seoul" => "Seoul",
            "Asia/Shanghai" => "Shanghai",
            "Asia/Singapore" => "Singapore",
            "Asia/Srednekolymsk" => "Srednekolymsk",
            "Asia/Taipei" => "Taipei",
            "Asia/Tashkent" => "Tashkent",
            "Asia/Tbilisi" => "Tbilisi",
            "Asia/Tehran" => "Tehran",
            "Asia/Thimphu" => "Thimphu",
            "Asia/Tokyo" => "Tokyo",
            "Asia/Tomsk" => "Tomsk",
            "Asia/Ulaanbaatar" => "Ulaanbaatar",
            "Asia/Urumqi" => "Urumqi",
            "Asia/Ust-Nera" => "Ust-Nera",
            "Asia/Vientiane" => "Vientiane",
            "Asia/Vladivostok" => "Vladivostok",
            "Asia/Yakutsk" => "Yakutsk",
            "Asia/Yangon" => "Yangon",
            "Asia/Yekaterinburg" => "Yekaterinburg",
            "Asia/Yerevan" => "Yerevan"
        );
    }

    public function get_time_zone_atlantic()
    {
        return array(
            "Atlantic/Azores" => "Azores",
            "Atlantic/Bermuda" => "Bermuda",
            "Atlantic/Canary" => "Canary",
            "Atlantic/Cape_Verde" => "Cape Verde",
            "Atlantic/Faroe" => "Faroe",
            "Atlantic/Madeira" => "Madeira",
            "Atlantic/Reykjavik" => "Reykjavik",
            "Atlantic/South_Georgia" => "South Georgia",
            "Atlantic/Stanley" => "Stanley",
            "Atlantic/St_Helena" => "St Helena"
        );
    }

    public function get_time_zone_australia()
    {
        return array(

            "Australia/Adelaide" => "Adelaide",
            "Australia/Brisbane" => "Brisbane",
            "Australia/Broken_Hill" => "Broken Hill",
            "Australia/Currie" => "Currie",
            "Australia/Darwin" => "Darwin",
            "Australia/Eucla" => "Eucla",
            "Australia/Hobart" => "Hobart",
            "Australia/Lindeman" => "Lindeman",
            "Australia/Lord_Howe" => "Lord Howe",
            "Australia/Melbourne" => "Melbourne",
            "Australia/Perth" => "Perth",
            "Australia/Sydney" => "Sydney"
        );
    }

    public function get_time_zone_europe()
    {
        return array(
            "Europe/Amsterdam" => "Amsterdam",
            "Europe/Andorra" => "Andorra",
            "Europe/Astrakhan" => "Astrakhan",
            "Europe/Athens" => "Athens",
            "Europe/Belgrade" => "Belgrade",
            "Europe/Berlin" => "Berlin",
            "Europe/Bratislava" => "Bratislava",
            "Europe/Brussels" => "Brussels",
            "Europe/Bucharest" => "Bucharest",
            "Europe/Budapest" => "Budapest",
            "Europe/Busingen" => "Busingen",
            "Europe/Chisinau" => "Chisinau",
            "Europe/Copenhagen" => "Copenhagen",
            "Europe/Dublin" => "Dublin",
            "Europe/Gibraltar" => "Gibraltar",
            "Europe/Guernsey" => "Guernsey",
            "Europe/Helsinki" => "Helsinki",
            "Europe/Isle_of_Man" => "Isle of Man",
            "Europe/Istanbul" => "Istanbul",
            "Europe/Jersey" => "Jersey",
            "Europe/Kaliningrad" => "Kaliningrad",
            "Europe/Kiev" => "Kiev",
            "Europe/Kirov" => "Kirov",
            "Europe/Lisbon" => "Lisbon",
            "Europe/Ljubljana" => "Ljubljana",
            "Europe/London" => "London",
            "Europe/Luxembourg" => "Luxembourg",
            "Europe/Madrid" => "Madrid",
            "Europe/Malta" => "Malta",
            "Europe/Mariehamn" => "Mariehamn",
            "Europe/Minsk" => "Minsk",
            "Europe/Monaco" => "Monaco",
            "Europe/Moscow" => "Moscow",
            "Europe/Oslo" => "Oslo",
            "Europe/Paris" => "Paris",
            "Europe/Podgorica" => "Podgorica",
            "Europe/Prague" => "Prague",
            "Europe/Riga" => "Riga",
            "Europe/Rome" => "Rome",
            "Europe/Samara" => "Samara",
            "Europe/San_Marino" => "San Marino",
            "Europe/Sarajevo" => "Sarajevo",
            "Europe/Saratov" => "Saratov",
            "Europe/Simferopol" => "Simferopol",
            "Europe/Skopje" => "Skopje",
            "Europe/Sofia" => "Sofia",
            "Europe/Stockholm" => "Stockholm",
            "Europe/Tallinn" => "Tallinn",
            "Europe/Tirane" => "Tirane",
            "Europe/Ulyanovsk" => "Ulyanovsk",
            "Europe/Uzhgorod" => "Uzhgorod",
            "Europe/Vaduz" => "Vaduz",
            "Europe/Vatican" => "Vatican",
            "Europe/Vienna" => "Vienna",
            "Europe/Vilnius" => "Vilnius",
            "Europe/Volgograd" => "Volgograd",
            "Europe/Warsaw" => "Warsaw",
            "Europe/Zagreb" => "Zagreb",
            "Europe/Zaporozhye" => "Zaporozhye",
            "Europe/Zurich" => "Zurich"
        );
    }

    public function get_time_zone_indian()
    {
        return array(
            "Indian/Antananarivo" => "Antananarivo",
            "Indian/Chagos" => "Chagos",
            "Indian/Christmas" => "Christmas",
            "Indian/Cocos" => "Cocos",
            "Indian/Comoro" => "Comoro",
            "Indian/Kerguelen" => "Kerguelen",
            "Indian/Mahe" => "Mahe",
            "Indian/Maldives" => "Maldives",
            "Indian/Mauritius" => "Mauritius",
            "Indian/Mayotte" => "Mayotte",
            "Indian/Reunion" => "Reunion"
        );
    }

    public function get_time_zone_pacific()
    {
        return array(
            "Pacific/Apia" => "Apia",
            "Pacific/Auckland" => "Auckland",
            "Pacific/Bougainville" => "Bougainville",
            "Pacific/Chatham" => "Chatham",
            "Pacific/Chuuk" => "Chuuk",
            "Pacific/Easter" => "Easter",
            "Pacific/Efate" => "Efate",
            "Pacific/Enderbury" => "Enderbury",
            "Pacific/Fakaofo" => "Fakaofo",
            "Pacific/Fiji" => "Fiji",
            "Pacific/Funafuti" => "Funafuti",
            "Pacific/Galapagos" => "Galapagos",
            "Pacific/Gambier" => "Gambier",
            "Pacific/Guadalcanal" => "Guadalcanal",
            "Pacific/Guam" => "Guam",
            "Pacific/Honolulu" => "Honolulu",
            "Pacific/Kiritimati" => "Kiritimati",
            "Pacific/Kosrae" => "Kosrae",
            "Pacific/Kwajalein" => "Kwajalein",
            "Pacific/Majuro" => "Majuro",
            "Pacific/Marquesas" => "Marquesas",
            "Pacific/Midway" => "Midway",
            "Pacific/Nauru" => "Nauru",
            "Pacific/Niue" => "Niue",
            "Pacific/Norfolk" => "Norfolk",
            "Pacific/Noumea" => "Noumea",
            "Pacific/Pago_Pago" => "Pago Pago",
            "Pacific/Palau" => "Palau",
            "Pacific/Pitcairn" => "Pitcairn",
            "Pacific/Pohnpei" => "Pohnpei",
            "Pacific/Port_Moresby" => "Port Moresby",
            "Pacific/Rarotonga" => "Rarotonga",
            "Pacific/Saipan" => "Saipan",
            "Pacific/Tahiti" => "Tahiti",
            "Pacific/Tarawa" => "Tarawa",
            "Pacific/Tongatapu" => "Tongatapu",
            "Pacific/Wake" => "Wake",
            "Pacific/Wallis" => "Wallis"
        );
    }

}
