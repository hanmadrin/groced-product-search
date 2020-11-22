add_action('wp_ajax_productsearchkey', 'productsearchkey');
add_action('wp_ajax_nopriv_productsearchkey', 'productsearchkey');
function productsearchkey()
{   
    $query = $_GET['query'];
    $query = trim($query,' ');
    $query = preg_replace('/\s+/', ' ',$query);
    $querya=$query;
    $query = explode(' ',$query);
    $queryc = count($query);
    $all_ids = get_posts( array(
        'post_type' => 'product',
        'numberposts' => -1,
        'orderby'=> 'title',
        'order' => 'ASC',
        'post_status' => 'publish',
        'fields' => 'ids',) );
    $matcht=0;
    for($i=0;$i<count($all_ids);$i++)
    {
        $k=0;
        $l=0;
        $title='#'.get_the_title($all_ids[$i]);
        for($j=0;$j<$queryc;$j++)
        {
            if(stripos($title,$query[$j]) != null)
            $k++;
        }
        if($k!=0)
        {
        $match[$k]=($match[$k]==null)?array():$match[$k];
        $match[$k]= array_merge($match[$k],array($all_ids[$i]));
        }
        
        
        if(stripos($title,$query[0]) != null)
        $rids[]=$all_ids[$i];   
    }
    $match[1]=($match[1]==null)?array():$match[1];
    $rids=($rids==null)?array():$rids;
    $match[0]=array_diff($rids,$match[1]);
    $rids=array_diff($rids,$match[0]);
    $match[1]=$rids;
    $datas['id']=array();
    for($j=$queryc;$j>=0;$j--)
       {
        if(count($match[$j])!=0)
        {
            $matcht = $matcht+count($match[$j]);
            $length=5-count($datas['id']);
            $datas['id'] = array_merge($datas['id'],array_slice($match[$j],0,$length));
        }
       }
    $datast=count($datas['id']);
    for($i=0;$i<$datast;$i++)
    {
        $products['id'][$i]=$datas['id'][$i];
        $product = wc_get_product( $products['id'][$i]);
        $products['title'][$i]=$product->get_name();
        $products['title'][$i]=strtolower($products['title'][$i]);
        for($j=0;$j<$queryc;$j++)
        {
            
            $query[$j]=strtolower($query[$j]);
            $products['title'][$i]=str_ireplace($query[$j],'<b>'.$query[$j].'</b>',$products['title'][$i]);
        }
        $products['price'][$i]=number_format($product->get_price(),2);
        $products['price'][$i]='&euro;'.str_replace('.',',',$products['price'][$i]);
        $products['img'][$i]=wp_get_attachment_url($product->get_image_id());

    }
    $products['total']=$matcht;
    $products['querya']=$querya;
    echo (json_encode($products));
    
    die();
}
