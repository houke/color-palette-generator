<?php
/**
 * Projekod Interactive
 * http://projekod.com
 */


class PKRoundColor {

    protected $baseColors = array(
        'FFEBEE', 'FFCDD2', 'EF9A9A', 'E57373', 'EF5350', 'F44336', 'E53935', 'D32F2F', 'C62828', 'B71C1C',
        'FCE4EC', 'F8BBD0', 'F48FB1', 'F06292', 'EC407A', 'E91E63', 'D81B60', 'C2185B', 'AD1457', '880E4F',
        'F3E5F5', 'E1BEE7', 'CE93D8', 'BA68C8', 'AB47BC', '9C27B0', '8E24AA', '7B1FA2', '6A1B9A', '4A148C',
        'EDE7F6', 'D1C4E9', 'B39DDB', '9575CD', '7E57C2', '673AB7', '5E35B1', '512DA8', '4527A0', '311B92',
        'E8EAF6', 'C5CAE9', '9FA8DA', '7986CB', '5C6BC0', '3F51B5', '3949AB', '303F9F', '283593', '1A237E',
        'E3F2FD', 'BBDEFB', '90CAF9', '64B5F6', '42A5F5', '2196F3', '1E88E5', '1976D2', '1565C0', '0D47A1',
        'E0F7FA', 'B2EBF2', '80DEEA', '4DD0E1', '26C6DA', '00BCD4', '00ACC1', '0097A7', '00838F', '006064',
        'E0F2F1', 'B2DFDB', '80CBC4', '4DB6AC', '26A69A', '009688', '00897B', '00796B', '00695C', '004D40',
        'E8F5E9', 'C8E6C9', 'A5D6A7', '81C784', '66BB6A', '4CAF50', '43A047', '388E3C', '2E7D32', '1B5E20',
        'F1F8E9', 'DCEDC8', 'C5E1A5', 'AED581', '9CCC65', '8BC34A', '7CB342', '689F38', '558B2F', '33691E',
        'F9FBE7', 'F0F4C3', 'E6EE9C', 'DCE775', 'D4E157', 'CDDC39', 'C0CA33', 'AFB42B', '9E9D24', '827717',
        'FFFDE7', 'FFF9C4', 'FFF59D', 'FFF176', 'FFEE58', 'FFEB3B', 'FDD835', 'FBC02D', 'F9A825', 'F57F17',
        'FFF8E1', 'FFECB3', 'FFE082', 'FFD54F', 'FFCA28', 'FFC107', 'FFB300', 'FFA000', 'FF8F00', 'FF6F00',
        'FFF3E0', 'FFE0B2', 'FFCC80', 'FFB74D', 'FFA726', 'FF9800', 'FB8C00', 'F57C00', 'EF6C00', 'E65100',
        'FBE9E7', 'FFCCBC', 'FFAB91', 'FF8A65', 'FF7043', 'FF5722', 'F4511E', 'E64A19', 'D84315', 'BF360C',
        'EFEBE9', 'D7CCC8', 'BCAAA4', 'A1887F', '8D6E63', '795548', '6D4C41', '5D4037', '4E342E', '3E2723',
        'FAFAFA', 'F5F5F5', 'EEEEEE', 'E0E0E0', 'BDBDBD', '9E9E9E', '757575', '616161', '424242', '212121',
        'ECEFF1', 'CFD8DC', 'B0BEC5', '90A4AE', '78909C', '607D8B', '546E7A', '455A64', '37474F', '263238',
        '000000', 'FFFFFF'
    );

    public function getBaseColors(){
        return $this->baseColors;
    }

    public function addBaseColor($color){
        if($this->checkHexColor($color)){
            $this->baseColors[] = $color;
            array_unique($this->baseColors);
        }
    }

    public function getRoundedColor($color){
        if($this->checkHexColor($color)){
            $rgbColor = $this->hex2rgb($color,true);
            $part = explode(",",$rgbColor);
            $redColor = $part[0];
            $greenColor = $part[1];
            $blueColor = $part[2];

            $distinction = array();

            $index = 0;
            foreach($this->baseColors as $baseColor){
                $baseColor = strtoupper($baseColor);
                $baseRgbColor = $this->hex2rgb($baseColor,true);
                $basePart = explode(",",$baseRgbColor);


                $baseRedColor = $basePart[0];
                $baseGreenColor = $basePart[1];
                $baseBlueColor = $basePart[2];

                $sqrt =sqrt(($redColor-$baseRedColor)*($redColor-$baseRedColor) +
                    ($greenColor-$baseGreenColor) * ($greenColor - $baseGreenColor) +
                    ($blueColor-$baseBlueColor) * ($blueColor - $baseBlueColor));

                $distinction["$sqrt"] = $index;
                $index++;

            }
            $minValue = min(array_keys($distinction));
            $index = $distinction[$minValue];
            return '#'.$this->baseColors[$index];
        }
    }

    public function checkHexColor($color){
        return preg_match('/^#[a-f0-9]{6}$/i', $color);
    }

    public function hex2rgb($hexVal,$implode=false) {
        $hexVal = str_replace("#", "", $hexVal);

        if(strlen($hexVal) == 3) {  //Like #000
            $red = hexdec(substr($hexVal,0,1).substr($hexVal,0,1));
            $green = hexdec(substr($hexVal,1,1).substr($hexVal,1,1));
            $blue = hexdec(substr($hexVal,2,1).substr($hexVal,2,1));
        } else {
            $red = hexdec(substr($hexVal,0,2));
            $green = hexdec(substr($hexVal,2,2));
            $blue = hexdec(substr($hexVal,4,2));
        }
        $rgb = array($red, $green, $blue);
        if($implode){
            return implode(",", $rgb);
        }
        return $rgb;
    }
}