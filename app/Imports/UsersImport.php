<?php

namespace App\Imports;

use App\Models\LopHoc;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Nullix\CryptoJsAes\CryptoJsAes;





class UsersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures, RemembersRowNumber;
    // , SkipsErrors;
    /**
     * @param array $row
     * 
     * 
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function __construct()
    {
        $this->rowsNotImport = [];
        $this->rowsImported = [];
    }

    public function check_group($value)
    {
        if ($value === 'Sinh viên') {
            $value = "sv";
        } else {
            $value = "gv";
        }

        return $value;
    }

    public function check_class($value)
    {
        $class = LopHoc::where('tenlop', (string)$value)->first();
        // if ($class) {
        // $user = User::where('malop', (string)$class->malop)->where('group', 'gv')->first();
        // if ($user) {
        //     return null;
        // } else {
        //     return $class->malop;
        // }
        // } else {
        //     return null;
        // }
        if($class)
        {
            return $class->malop;
        }else
        {
            return null;
        }
    }

    public function check_sex($value)
    {
        if ($value === "Nam") {
            return "male";
        } else if ($value === "Nữ") {
            return "female";
        }
    }

    public function get_latest_user()
    {
        $latest_user = DB::table('users')->latest()->first();
        return $latest_user['mauser'];
    }

    public function set_new_mauser()
    {
        $current_latest_id = $this->get_latest_user();
        $new_id = "";
        $arr = explode("u", $current_latest_id);
        $number = (int)$arr[1];
        if ($number < 9) {
            $new_id = "u0" . (string)($number + 1);
        } else if ($number >= 9) {
            $new_id = "u" . (string)($number + 1);
        }
        return ($new_id);
    }

    public function model(array $row)
    {
        $key = (string)config('app.key');
        $group = $this->check_group($row['group']);
        $malop = $row["classname"];

        $sex = $this->check_sex($row["sex"]);

        $new_mauser = $this->set_new_mauser();
        $userController = new UserController();

        
            $data = [
                'name'     => $row["name"],
                'email'    => $row["email"],
                'password' => Hash::make($row["password"]),
                'group' => $group,
                'is_updated_info' => 0,
                'sdt' => $row["sdt"],
                'malop' => $malop,
                'sex' => $sex,
                'active' => false,
                'code' => $userController->set_new_code(),
                'mauser' => (string)$new_mauser,
            ];
            if ($row["email"] !== null) {
                array_push($this->rowsImported, [
                    'username' => $row['email'],
                    'password' => CryptoJsAes::encrypt($row['password'], $key),
                ]);
            }
            return new User($data);
        
    }

    // public function onError(\Throwable $error){}\

    public function rules(): array
    {
        return [
            '*.email' => [
                'unique:users,email',
                // 'regex:/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD'
            ],
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.email.unique' => 'Email này đã tồn tại.',
            // '*.email.regex' => 'Bạn cần nhập vào định dạng email.',
        ];
    }

    public function getRowsNotImport(): array
    {
        return $this->rowsNotImport;
    }

    public function getRowsImported(): array
    {
        return $this->rowsImported;
    }
    // public function onFailure(Failure ...$failure){}

}
