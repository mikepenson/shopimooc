<?php
/**
 * Created by PhpStorm.
 * User: melonydi
 * Date: 2016/7/12
 * Time: 22:20
 */
//require_once "../include.php";
//$sql="select * from imooc_admin";
//$totalRows=getResultNum($sql);
////echo $totalRows;
//$pageSize=2;
////得到总页码数
//$totalPages=ceil($totalRows/$pageSize);
//$page=$_REQUEST['page']?(int)$_REQUEST['page']:1;
//if ($page<1||$page==null||!is_numeric($page)){
//    $page=1;
//}
//if ($page>=$totalPages) $page=$totalPages;
//$offset=($page-1)*$pageSize;
//$sql="select * from imooc_admin limit {$offset},{$pageSize}";
//
//$rows=fetchAll($sql);
////print_r($rows);
//foreach ($rows as $row){
//    echo "编号：".$row['id']."<br/>";
//    echo "管理员名称：".$row['username']."<hr/>";
//}
//echo showPage($page,$totalPages);
//echo "<hr/>";
//echo showPage($page,$totalPages,"cid=4");
function showPage($page,$totalPages,$where=null,$sep="&nbsp;"){
    $where=$where==null?null:"&".$where;
    $url=$_SERVER['PHP_SELF'];
    $index=($page==1)?"首页":"<a href='{$url}?page=1{$where}'>首页</a>";
    $last=($page==$totalPages)?"尾页":"<a href='{$url}?page={$totalPages}{$where}'>尾页</a>";
    $pre=($page==1)?"上一页":"<a href='{$url}?page=".($page-1)."{$where}'>上一页</a>";
    $next=($page==$totalPages)?"下一页":"<a href='{$url}?page=".($page+1)."{$where}'>下一页</a>";
    $str="总共{$totalPages}页/当前是第{$page}页";
    $p="";
    for ($i=1;$i<=$totalPages;$i++){
        //当前页无链接
        if ($page==$i){
            $p.="[{$i}]";
        }else{
            $p.="<a href='{$url}?page={$i}'>[{$i}]</a>";
        }
    }
    $pageStr=$str.$sep.$index.$sep.$pre.$sep.$p.$sep.$next.$sep.$last;
    return $pageStr;
}