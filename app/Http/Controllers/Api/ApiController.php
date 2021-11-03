<?php

namespace App\Http\Controllers\Api;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{

    private function query($key) {
        switch ($key) {
            case 0: return '{"operationName":"countries","query":"query countries {\n  totalDeaths\n  provinces {\n    Confirmed\n    Deaths\n    Recovered\n    __typename\n  }\n  totalDeathsLast\n  trendlineGlobalCases {\n    date\n    death\n    __typename\n  }\n}\n"}';
            case 1: return '{"operationName":"countries","query":"query countries {\n  totalRecovered\n  provinces {\n    Confirmed\n    Deaths\n    Recovered\n    __typename\n  }\n  totalRecoveredLast\n  trendlineGlobalCases {\n    date\n    recovered\n    __typename\n  }\n}\n"}';
            case 2: return '{"operationName":"provinces","query":"query provinces {\n  provinces {\n    Province_Name\n    Province_Id\n    Lat\n    Long\n    Confirmed\n    Deaths\n    Recovered\n    Last_Update\n    __typename\n  }\n  totalVietNam {\n    confirmed\n    deaths\n    recovered\n    __typename\n  }\n}\n"}';
            case 3: return '{"operationName":null,"query":"{\n  globalCasesToday {\n    country\n    totalCase\n    totalDeaths\n    totalRecovered\n    longitude\n    latitude\n    __typename\n  }\n  totalVietNam {\n    confirmed\n    deaths\n    recovered\n    __typename\n  }\n}\n"}';
            case 4: return '{"operationName":"vnPatientCases","query":"query vnPatientCases {\n  vnPatientCases {\n    patient\n    age\n    gender\n    location\n    status\n    nationality\n    __typename\n  }\n}\n"}';
            case 5: return '{"operationName":"trendlineVnCases","query":"query trendlineVnCases {\n  trendlineVnCases {\n    date\n    confirmed\n    recovered\n    deaths\n    __typename\n  }\n}\n"}';
            case 6: return '{"operationName":"topTrueNews","query":"query topTrueNews {\n  topTrueNews {\n    id\n    type\n    title\n    content\n    url\n    siteName\n    publishedDate\n    author\n    picture\n    __typename\n  }\n}\n"}';
            case 7: return '{"operationName":"totalVietNam","query":"query totalVietNam {\n  totalVietNam {\n    confirmed\n    deaths\n    recovered\n    __typename\n  }\n}\n"}';
            case 8: return '{"operationName":"totalConfirmed","query":"query totalConfirmed {\n  totalConfirmed\n  totalConfirmedLast\n  trendlineGlobalCases {\n    date\n    confirmed\n    __typename\n  }\n}\n"}';
            case 9: return '{"operationName":"provinces","query":"query provinces {\n  provinces {\n    Province_Name\n    Province_Id\n    Lat\n    Long\n    Confirmed\n    Deaths\n    Recovered\n    Last_Update\n    __typename\n  }\n}\n"}';
        }
    }

    function call_source_api(Request $request) {
        if(isset($request->key) == false) return response()->json([
            "key"   => "các trường trả về",
            "k0"     => [
                "totalDeaths: tổng ca chết TG",
                "provinces (Confirmed, Deaths, Recovered, __typename): các tỉnh VN",
                "totalDeathsLast",
                "trendlineGlobalCases (date, death,__typename): ca chết TG theo ngày",
            ],
            "k1"     => [
                "totalRecovered: tổng ca khỏi TG",
                "provinces (Confirmed, Deaths, Recovered, __typename): các tỉnh VN",
                "totalRecoveredLast",
                "trendlineGlobalCases (date, recovered, __typename): ca khỏi TG theo ngày",
            ],
            "k2"     => [
                "provinces (Province_Name, Province_Id, Lat, Long, Confirmed, Deaths, Recovered, Last_Update, __typename): chi tiết các tỉnh",
                "totalVietNam (confirmed, deaths, recovered, __typename)"
            ],
            "k3"     => [
                "globalCasesToday (country, totalCase, totalDeaths, totalRecovered, longitude, latitude, __typename): chi tiết các QG",
                "totalVietNam (confirmed, deaths, recovered, __typename)",
            ],
            "k4"     => [
                "vnPatientCases: chi tiết ca mắc bệnh VN",
                "(patient, age, gender, location, status, nationality, __typename)",
            ],
            "k5"     => [
                "trendlineVnCases",
                "(date, confirmed, recovered, deaths, __typename)",
            ],
            "k6"     => [
                "topTrueNews",
                "id, type, title, content, url, siteName, publishedDate, author, picture, __typename)"
            ],
            "k7"     => [
                "totalVietNam (confirmed, deaths, recovered, __typename)",
            ],
            "k8?"     => [
                "totalConfirmed: tổng ca mắc TG",
                "totalConfirmedLast",
                "trendlineGlobalCases(date, confirmed, __typename): ca mắc TG theo ngày",
            ],
            "k9"     => [
                "provinces: chi tiết các tỉnh",
                "(Province_Name, Province_Id, Lat, Long, Confirmed, Deaths, Recovered, Last_Update, __typename)",
            ],
        ]);

        $client =  new Client();
        $key = (int)$request->key;

        if($key<0 || $key>9) return response()->json([
            "Hỏi cái" => "Gọi sai vậy vui hông?"
        ], 404);

        $api = 'https://corona-api.kompa.ai/graphql';
        $query = $this->query($key);

        $result = $client->post($api, [
            'headers'   => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
            'body'      => $query
        ]);

        return response()->json([
            "sourceurl"     => "https://corona.kompa.ai/",
            "lastupdated"   => date('Y-m-d H:i', strtotime('- 300 seconds')).':00',
            "author"        => "https://fb.quocbaoit.com/",
            "buymeacoffee"  => [
                "momo"      => "https://nhantien.momo.vn/0786058166",
                "paypal"    => "micaetranquocbao2001@gmail.com",
                "bank"      => "1903 5336 765 012, Techcombank, TRAN QUOC BAO",
                "note"      => "Nói buy coffee thôi chứ thật ra là học phí hè này 4 triệu =))"
            ],
            "data"          => json_decode($result->getBody())->data,
        ]);
    }



}
