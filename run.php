<?php
error_reporting(0);
ini_set("memory_limit", '-1');
include 'func.php';
echo "Udemy Downloader | Code by sandroputraa\n\n";
function get_list_download($id, $header_udemy, $name_dir, $quality_answers, $quality_answer)
{
    echo "\n\n";
    if (!is_dir('video/'.$name_dir.'')) {
        $name_dir_fix = str_replace(array("\\","/","(",")",":","|","<",">","?","*","\"", " "), "-", $name_dir);
        mkdir('video/'.$name_dir_fix.'');
        $dir_fix = 'video/'.$name_dir_fix.'/';
    }else{
        echo "[!] Directory is exist\n";
    }
    $get_list = curl(
        'https://www.udemy.com/api-2.0/courses/'.$id.'/subscriber-curriculum-items/?page_size=1400&fields[lecture]=title,object_index,is_published,sort_order,created,asset,supplementary_assets,is_free&fields[quiz]=title,object_index,is_published,sort_order,type&fields[practice]=title,object_index,is_published,sort_order&fields[chapter]=title,object_index,is_published,sort_order&fields[asset]=title,filename,asset_type,status,time_estimation,is_external&caching_intent=True',
        'GET',
        null,
        null,
        $header_udemy
    );
    $count = json_decode($get_list[2], true)['count'];
    echo "[!] Total Video :".$count."\n";
    for ($i=0; $i <$count; $i++) {
        $j_d = json_decode($get_list[2], true);
        $get_link = curl(
            'https://www.udemy.com/api-2.0/users/me/subscribed-courses/'.$id.'/lectures/'.$j_d['results'][$i]['id'].'/?fields[lecture]=asset,description,download_url,is_free,last_watched_second&fields[asset]=asset_type,length,stream_urls,captions,thumbnail_sprite,slides,slide_urls,download_urls',
            'GET',
            null,
            null,
            $header_udemy
        );
        if (strpos($get_link[2], 'Resource not found.')) {
            echo "[!] Skip Not Video \n";
        } else {
            foreach (json_decode($get_link[2], true)['asset']['stream_urls']['Video'] as $key => $value) {
                if ($quality_answers == $value['label']) {
                    echo "[".$i."] Downloading Video : ".$j_d['results'][$i]['title']." - ".$value['label']."p - saved in ".$dir_fix."\n";
                    $judul = str_replace(array("\\","/","(",")",":","|","<",">","?","*","\""), " ", $j_d['results'][$i]['title']);
                    file_put_contents($dir_fix."" . $judul . ".mp4", file_get_contents($value['file']));
                    break;
                }
                if ($quality_answers !== $value['label']) {
                    echo "[".$i."] Downloading Video : ".$j_d['results'][$i]['title']." - ".$value['label']."p - saved in ".$dir_fix."\n";
                    $judul = str_replace(array("\\","/","(",")",":","|","<",">","?","*","\""), " ", $j_d['results'][$i]['title']);
                    file_put_contents($dir_fix."" . $judul . ".mp4", file_get_contents($value['file']));
                    break;
                }
            }
            continue;
        }
    }
}

$token = getVarFromUser('Token ');
$action = getVarFromUser('1.Mass Download | 2.Single Download ');

$header_udemy = [
    "accept: application/json, text/plain, */*",
    "accept-language: en-US,en;q=0.9,id;q=0.8",
    "host: www.udemy.com",
    "sec-fetch-dest: empty",
    "sec-fetch-mode: cors",
    "sec-fetch-site: same-origin",
    "user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.102 Safari/537.36",
    "x-requested-with: XMLHttpRequest",
    "x-udemy-authorization: Bearer ".$token."",
    "x-udemy-cache-brand: IDen_US",
    "x-udemy-cache-device: desktop",
    "x-udemy-cache-logged-in: 1",
    "x-udemy-cache-marketplace-country: ID",
    "x-udemy-cache-price-country: ID",
    "x-udemy-cache-version: 1"
];

switch ($action) {
    case '1':
        echo "[+] Getting List Your course \n";
        $list_course = curl(
            'https://www.udemy.com/api-2.0/users/me/subscribed-courses/?ordering=-last_accessed&fields[course]=@min,visible_instructors,image_240x135,favorite_time,archive_time,completion_ratio,last_accessed_time,enrollment_time,is_practice_test_course,features,num_collections,published_title,is_private,buyable_object_type&fields[user]=@min,job_title&page=1&page_size=12',
            'GET',
            null,
            null,
            $header_udemy
        );
        $json_decode = json_decode($list_course[2], true);
        echo "[+] Total Course : ".$json_decode['count']."\n\n";
        selects:
        echo "\n[!] Select Quality : 
        [0] 1080p
        [1] 720p
        [2] 480p\n\n";
        $quality_answer = getVarFromUser("[!] Select quality video course to download");
        if ($quality_answer == 0) {
            $quality_answers = '1080';
        } elseif ($quality_answer == 1) {
            $quality_answers = '720';
        } elseif ($quality_answer == 2) {
            $quality_answers = '480';
        }else{
            echo "[x] Failed get quality";
            goto selects;
        }
        for ($i=0; $i <$json_decode['count']; $i++) {
            echo "[".$i."] Title : ".$json_decode['results'][$i]['title']."\n";
        
            $id_course = $json_decode['results'][$i]['id'];
            $name_dir = $json_decode['results'][$i]['title'];

            $download_course = get_list_download($id_course, $header_udemy, $name_dir, $quality_answers, $quality_answer);
            echo $download_course;
        }
        break;
        case '2':
            echo "[+] Getting List Your course \n";
            $list_course = curl(
                'https://www.udemy.com/api-2.0/users/me/subscribed-courses/?ordering=-last_accessed&fields[course]=@min,visible_instructors,image_240x135,favorite_time,archive_time,completion_ratio,last_accessed_time,enrollment_time,is_practice_test_course,features,num_collections,published_title,is_private,buyable_object_type&fields[user]=@min,job_title&page=1&page_size=12',
                'GET',
                null,
                null,
                $header_udemy
            );
            $json_decode = json_decode($list_course[2], true);
            echo "[+] Total Course : ".$json_decode['count']."\n\n";
            for ($i=0; $i <$json_decode['count']; $i++) {
                echo "[".$i."] Title : ".$json_decode['results'][$i]['title']."\n";
            }
            $course_answers = getVarFromUser("[!] Select number course to download");
            $id_course = $json_decode['results'][$course_answers]['id'];
            $name_dir = $json_decode['results'][$course_answers]['title'];
            select:
            echo "\n[!] Select Quality : 
            [0] 1080p
            [1] 720p
            [2] 480p\n\n";
            $quality_answer = getVarFromUser("[!] Select quality video course to download");
    
            if ($quality_answer == 0) {
                $quality_answers = '1080';
            } elseif ($quality_answer == 1) {
                $quality_answers = '720';
            } elseif ($quality_answer == 2) {
                $quality_answers = '480';
            }else{
                echo "[x] Failed get quality";
                goto select;
            }
            $download_course = get_list_download($id_course, $header_udemy, $name_dir, $quality_answers, $quality_answer);
            echo $download_course;
            break;
    default:
        die("[X] No menu\n");
        break;
}
