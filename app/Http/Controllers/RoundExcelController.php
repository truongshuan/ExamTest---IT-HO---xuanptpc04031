<?php

namespace App\Http\Controllers;

use App\Exports\BracketExport;
use App\Imports\Data;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;

class RoundExcelController extends Controller
{
    /**
     * Thực hiện thuật toán round robin để chia cặp đấu giữa các người chơi trong danh sách.
     *
     * @param array $lst Mảng chứa tên hoặc mã số của các người chơi
     * @return array Mảng chứa các cặp đấu cho từng vòng
     */
    public function roundrobin($lst)
    {
        // Đếm số lượng người chơi
        $n = count($lst);

        // Nếu số lượng người chơi lẻ, thêm một người chơi rỗng để đảm bảo số lượng chẵn
        if ($n % 2) {
            $lst[] = null;
            $n++;
        }

        // Mảng lưu trữ các cặp đấu cho từng vòng
        $matchups = [];

        // Lặp qua các vòng đấu
        for ($i = 0; $i < $n - 1; $i++) {
            // Chia danh sách làm đôi
            $mid = $n / 2;
            $l1 = array_slice($lst, 0, $mid);  // Nửa đầu danh sách
            $l2 = array_slice($lst, $mid);    // Nửa cuối danh sách

            // Đảo ngược nửa cuối danh sách để tạo các cặp đấu chéo
            $l2 = array_reverse($l2);

            // Tạo các cặp đấu cho vòng này
            $round = [];
            for ($j = 0; $j < $mid; $j++) {
                // Chỉ tạo cặp đấu nếu cả hai người chơi đều không rỗng và tồn tại khóa $j trong $l1 và $l2
                if (isset($l1[$j]) && isset($l2[$j]) && $l1[$j] && $l2[$j]) {
                    $round[] = [$l1[$j], $l2[$j]];
                }
            }
            // Thêm các cặp đấu của vòng này vào mảng chung
            $matchups[] = $round;
            // Xoay vòng danh sách người chơi để tạo các cặp đấu mới ở vòng sau
            array_splice($lst, 1, 0, array_pop($lst));
        }

        // Trả về mảng chứa các cặp đấu cho tất cả các vòng
        return $matchups;
    }


    /**
     * Xử lý dữ liệu đầu vào từ file excel và xuất sơ đồ giải đấu
     *
     * @param Request $request
     * @return void
     */
    public function processExcel(Request $request)
    {
        $file = $request->file('file');
        $path1 = $request->file('file')->store('temp');
        $path = storage_path('app') . '/' . $path1;
        // Dữ liệu đầu vào
        $data = Excel::toArray(new Data, $file, 'xlsx');

        $names = [];
        // Lấy danh sách đơn vị
        for ($i = 1; $i < count($data[0]); $i++) {
            $names[] = $data[0][$i][1];
        }
        $bracket = $this->roundrobin(array_values($names));

        // Xuất mảng danh sách các cặp đấu
        // dd($bracket);

        // Vẽ sơ đồ giải đấu
        return Excel::download(new BracketExport($bracket), 'bracket.xlsx');
    }
}
