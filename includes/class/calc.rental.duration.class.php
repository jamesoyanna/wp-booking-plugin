<?php 
namespace BackhoeBooking;

use \DateTime;
class Backhoe_Booking_CalcDuration {
    public static function calcDay($producte_id=null,$start_date,$end_date) {
        $pricePer = get_post_meta($producte_id,'_pricing_type',true);

        $total = 0;

        switch ($pricePer) {
            case 'hourly_pricing':
                $start_date = new DateTime($start_date);
                $end_date = new DateTime($end_date);
                $interval = $start_date->diff($end_date);
                $total = $interval->format('%d Day %h h');
                break;
            case 'daily_pricing':
                $datediff = strtotime($end_date) - strtotime($start_date);
                $total = round($datediff / (60 * 60 * 24))."<span> ".esc_html__('Days','backhoebooking')." </span>";
                break;
            case 'weekly_pricing':
                $start_date = new DateTime($start_date);
                $end_date = new DateTime($end_date);
                $total = ceil($start_date->diff($end_date)->days/7)."<span> ".esc_html__('Weeks','backhoebooking')." </span>";
                
                    break;
        }
        return $total;
    }

    public static function calcPice($producte_id,$start_date,$end_date, $price) {
        $order_quantity = (isset($_POST['quantity'])) ? $_POST['quantity'] : 1;
        $pricePer = get_post_meta($producte_id,'_pricing_type',true);
        $datediff = strtotime($end_date) - strtotime($start_date);

        $pricing_methode = get_post_meta($producte_id,'devia_pricing_methode',true);

        $totalPrice = 0;

        switch ($pricing_methode) {
            case 'price_per':        
                switch ($pricePer) {
                    case 'hourly_pricing':                
                        $hours = floor(($datediff ) / (60*60) );
                        $totalPrice = $hours * $price * $order_quantity;
                        break;

                    case 'daily_pricing':
                        $total_days = round($datediff / (60 * 60 * 24));
                        $totalPrice = $total_days * $price * $order_quantity;
                        break;
                    case 'weekly_pricing':
                        $start_date = new DateTime($start_date);
                        $end_date = new DateTime($end_date);
                        $total_week = ceil($start_date->diff($end_date)->days/7);
                        $totalPrice = $total_week * $price * $order_quantity;
                            break;
                }
                break;
            case 'price_fixed':
                $totalPrice = $price;
                break; 
            case 'price_structure':    
                $structeurID = get_post_meta($producte_id,'structur_pricing',true);
                $total_days = round($datediff / (60 * 60 * 24));
                $structeurCalc = get_post_meta($structeurID,'pricing_structure_fields',true);
                    if($structeurCalc == '') {
                        $structeurCalc = array(array('duration'=>'1','multiplier'=>'1',));
                    }
                $extra_multiplier = get_post_meta($structeurID,'extra_multiplier',true);
                foreach($structeurCalc as $value ){
                    if($value['duration'] > $total_days ) {
                        $totalPrice =  $value['multiplier'] * $price;
                    }else{
                        $difday = $total_days - $value['duration'];
                        $firstPeriodPrice =  $value['multiplier'] * $price;
                        $secPeriodPrice =  $extra_multiplier * $price * $difday;
                        $totalPrice = $firstPeriodPrice + $secPeriodPrice;
                    }
                    
                }

                 // TODO to change
                

                break; 


            default:                
                $totalPrice = $price;
                break; 
            }



        return $totalPrice;
    }
}
