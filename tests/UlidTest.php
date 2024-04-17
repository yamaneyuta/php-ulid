<?php
use PHPUnit\Framework\TestCase;
use yutayamane\Ulid;

class UlidTest extends TestCase
{
    /**
     * @test
     * @testdox 出力される文字列のフォーマットチェック
     * @dataProvider fromParams
     */
    public function testFormat(): void
    {
        $ulid = new Ulid();
        
        // ULID文字列
        $this->myAssertMatchesRegularExpression('/^[0-9A-Z]{26}$/', $ulid->toString());
        $this->myAssertMatchesRegularExpression('/^[0-9A-Z]{26}$/', (string)$ulid);

        // UUID形式で出力
        $this->assertTrue( $this->isUuidFormat($ulid->toUuid()));
        
        // 16進数で出力
        $this->myAssertMatchesRegularExpression('/^[0-9a-f]+$/', $ulid->toHex());
    }

    /**
     * @test
     * @testdox ULIDオブジェクトから出力した文字列を使って再度ULIDオブジェクトを生成したときに同一の内容になるかどうかを確認
     * 生成したULIDオブジェクトから文字列を取得。その文字列を再度ULIDオブジェクトに戻し、そこから文字列を再取得。
     * 2つの文字列が一致することを確認
     */
    public function testReconvert(): void
    {
        $ulid = new Ulid();

        // ULID文字列をオブジェクトを生成し、そのULID文字列を再度取得して比較
        $this->assertEquals($ulid->toString(), Ulid::from($ulid->toString())->toString());
        // UUID形式の文字列からオブジェクトを生成し、そのUUID形式の出力を再度取得して比較
        $this->assertEquals($ulid->toUuid(), Ulid::from($ulid->toUuid())->toUuid());
        // 16進数の文字列からオブジェクトを生成し、その16進数の出力を再度取得して比較
        $this->assertEquals($ulid->toHex(), Ulid::from($ulid->toHex())->toHex());
        // 16進数は文字列の先頭に'0x'が付与されていても問題ない
        $this->assertEquals($ulid->toHex(), Ulid::from('0x' . $ulid->toHex())->toHex());
    }

    /**
     * @test
     * @testdox 生成したULIDオブジェクトから時間を取得し、生成した時間におおよそ一致することを確認
     */
    public function testGetTime(): void {
        // 現在時刻をマイクロ秒の数値型で取得
        $now = microtime(true);
        $ulid = new Ulid();

        // $ulid->getTime()の戻り値が$nowの前後0.1秒に収まっていることを確認
        $this->assertGreaterThan($now - 0.1, $ulid->getTime());
        $this->assertLessThan($now + 0.1, $ulid->getTime());
    }

    /**
     * @test
     * @testdox ULID文字列からオブジェクトを生成し、UUID形式の文字列を取得
     * @dataProvider fromParams
     */
    public function testFromUlid( string $ulid_str, string $uuid_str, string $timestamp_str ): void {

        $ulid = Ulid::from( $ulid_str );
        // UUIDが一致していることを確認
        $this->assertEquals(strtolower($uuid_str), strtolower($ulid->toUuid()));

        // 時間を数値型に変換
        $timestamp = $this->timestampStrToFloat($timestamp_str);
        // 時間も一致することを確認
        $this->assertEquals(sprintf('%.3f', $timestamp), sprintf('%.3f', $ulid->getTime()));
    }

    /**
     * @test
     * @testdox UUID文字列からオブジェクトを生成し、ULID形式の文字列を取得
     * @dataProvider fromParams
     */
    public function testFromUuid( string $ulid_str, string $uuid_str, string $timestamp_str ): void {
        $ulid = Ulid::from( $uuid_str );
        // ULIDが一致していることを確認
        $this->assertEquals($ulid_str, $ulid->toString());
        $this->assertEquals($ulid_str, (string)$ulid);

        // 時間を数値型に変換
        $timestamp = $this->timestampStrToFloat($timestamp_str);
        // 時間も一致することを確認
        $this->assertEquals(sprintf('%.3f', $timestamp), sprintf('%.3f', $ulid->getTime()));
    }
    
    /**
     * @test
     * @testdox 16進数文字列からオブジェクトを生成し、ULID形式の文字列とUUID形式の文字列を取得
     * @dataProvider fromParams
     */
    public function testFromHex( string $ulid_str, string $uuid_str, string $timestamp_str ): void {
        // $uuid_strのハイフンを削除
        $hex = str_replace('-', '', $uuid_str);

        $ulid = Ulid::from( $hex );
        // ULIDが一致していることを確認
        $this->assertEquals($ulid_str, $ulid->toString());
        $this->assertEquals($ulid_str, (string)$ulid);
        // UUIDが一致していることを確認
        $this->assertEquals(strtolower($uuid_str), strtolower($ulid->toUuid()));
    }

    public static function fromParams(): array {
        return [
            // [ ULID文字列, UUID文字列, 時間文字列 ]
            ['01HVK10KGQMF83JMCQ0KHK5PG6', '018EE610-4E17-A3D0-3951-9704E332DA06', '2024-04-16T08:40:12.055Z' ],
            [ "00000000000000000000000000", "00000000-0000-0000-0000-000000000000", "1970-01-01T00:00:00.000Z"],
            [ '76EZ91ZPZZZZZZZZZZZZZZZZZZ', 'E677D21F-DBFF-FFFF-FFFF-FFFFFFFFFFFF', '9999-12-31T23:59:59.999Z'],

            [ "0F1W7GY3RF1W7GY3RF1W7GY3RF", "0F0F0F0F-0F0F-0F0F-0F0F-0F0F0F0F0F0F", "2494-09-06T00:19:31.215Z"],
            [ "00Y07G1W0F03R0Y07G1W0F03R0", "00F00F00-F00F-00F0-0F00-F00F00F00F00", "2002-09-03T09:04:30.735Z"],
            ["0H248H248H248H248H248H248H", "11111111-1111-1111-1111-111111111111", "2564-08-21T11:34:07.377Z"],
            ["1248H248H248H248H248H248H2", "22222222-2222-2222-2222-222222222222", "3159-04-12T23:08:14.754Z"],
        ];
    }

    /**
     * @test
     * @testdox ULID文字列が不正な場合に例外が発生することを確認
     * @dataProvider fromValueUlidParams
     * @param string $ulid_str 
     */
    public function testFromValueUlid( string $ulid_str, bool $is_valid ): void {
        try {
            Ulid::from( $ulid_str );
            $this->assertTrue($is_valid);
        }
        catch (Exception $e) {
            $this->assertFalse($is_valid);
            $this->assertEquals('Invalid ULID format.', $e->getMessage());
        }
    }

    public static function fromValueUlidParams(): array{
        // ここでは文字列の長さが26文字のものを対象とする
        return [
            // [ ULID文字列, ULID文字列として有効かどうか ]
            ["00000000000000000000000000", true],
            ["7ZZZZZZZZZZZZZZZZZZZZZZZZZ", true], // max
            ["01HVK10KGQMF83JMCQ0KHK5PG6", true],

            // ULIDは`7ZZZZZZZZZZZZZZZZZZZZZZZZZ`が最大値
            ["80000000000000000000000000", false],
            ["8ZZZZZZZZZZZZZZZZZZZZZZZZZ", false],
            ["ZZZZZZZZZZZZZZZZZZZZZZZZZZ", false],
            // ULIDに含まれない文字`I`が入っている
            ["I0000000000000000000000000", false],
            ["000000000000I0000000000000", false],
            ["0000000000000000000000000I", false],
        ];
    }

    /**
     * @test
     * @testdox UUID文字列が不正な場合に例外が発生することを確認
     * @dataProvider fromValueUuidParams
     * @param string $uuid
     * @return void 
     */
    public function testFromValueUuid( string $uuid, bool $is_valid ): void {
        try {
            Ulid::from( $uuid );
            $this->assertTrue($is_valid);
        }
        catch (Exception $e) {
            $this->assertFalse($is_valid);
            $this->assertEquals('Invalid UUID format.', $e->getMessage());
        }
    }

    public static function fromValueUuidParams(): array{
        // ここでは文字列の長さが36文字のものを対象とする
        return [
            // [ UUID文字列, UUID文字列として有効かどうか ]
            ["00000000-0000-0000-0000-000000000000", true],
            ["FFFFFFFF-FFFF-FFFF-FFFF-FFFFFFFFFFFF", true],
            ["ffffffff-ffff-ffff-ffff-ffffffffffff", true],// 小文字
            ["018EE610-4E17-A3D0-3951-9704E332DA06", true],
            ["018ee610-4e17-a3d0-3951-9704e332da06", true],// 小文字

            // UUIDに含まれない文字`G`が入っている
            ["G0000000-0000-0000-0000-000000000000", false],
            ["00000000-0000-G000-0000-000000000000", false],
            ["00000000-0000-0000-0000-00000000000G", false],

            // ハイフンが入っていない
            ["000000000000000000000000000000000000", false],
            // ハイフンの位置が不正
            ["0000-00000000-0000-0000-000000000000", false],
        ];
    }

    /**
     * @test
     * @testdox 16進数文字列が不正な場合に例外が発生することを確認
     * @dataProvider fromValueHexParams
     * @param string $hex 
     * @param bool $is_valid 
     */
    public function testFromValueHex( string $hex, bool $is_valid ): void {
        try {
            Ulid::from( $hex );
            $this->assertTrue($is_valid);
        }
        catch (Exception $e) {
            $this->assertFalse($is_valid);
            $this->assertEquals('Invalid hex format.', $e->getMessage());
        }
    }

    public static function fromValueHexParams(): array{
        return [
            ["00000000000000000000000000000000", true],
            ["0x00000000000000000000000000000000", true],
            ["FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF", true],
            ["0xFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF", true],
            ["ffffffffffffffffffffffffffffffff", true],
            ["0xffffffffffffffffffffffffffffffff", true],

            // 16進数に含まれない文字`g`が入っている場合はNG
            ["g0000000000000000000000000000000", false],
            ["0000000000000000g000000000000000", false],
            ["0000000000000000000000000000000g", false],

            // `0x`を除いた文字数が1文字(32未満)の場合はOK
            ["0", true ],
            ["0x0", true ],
            ["f", true ],
            ["0xf", true ],

            // `0x`を除いた文字数が31文字(32未満)の場合はOK
            ["00000000000000000000000000000000", true ],
            ["0x00000000000000000000000000000000", true ],
            ["FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF", true],
            ["0xFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF", true],
            ["fffffffffffffffffffffffffffffff", true],
            ["0xfffffffffffffffffffffffffffffff", true],

            // `0x`を除いた文字数が33文字(32より大きい)の場合はNG
            ["000000000000000000000000000000000", false],
            ["0x000000000000000000000000000000000", false],
            
        ];
    }

    /**
     * 指定された文字列がUUID形式かどうかを判定
     * - 使用されている文字は16進数またはハイフン
     * - ハイフンの位置
     * - 文字数
     * @param string $uuid 
     * @return bool UUIDのフォーマットであればtrue
     */
    private function isUuidFormat( string $uuid ): bool {
        $pattern = '/^([0-9a-f]{8})-([0-9a-f]{4})-([0-9a-f]{4})-([0-9a-f]{4})-([0-9a-f]{12})$/';
        return preg_match($pattern, $uuid) === 1;
    }

    /**
     * `2024-04-16T08:40:12.055Z`のような書式の文字列をUNIXタイムスタンプ(小数点以下3桁)のfloat型に変換
     * @param string $timestamp 
     * @return float UNIXタイムスタンプ(小数点以下3桁)
     */
    private function timestampStrToFloat( string $timestamp ): float {
        // 小数点以下を抽出し、$timestampにプラスして返す
        preg_match('/\.\d+/', $timestamp, $matches);
        return strtotime($timestamp) + (float)("0." . substr($matches[0], 1));
    }
    

    /**
     * assertMatchesRegularExpressionのラッパー
     * PHPUnitのバージョンによってはassertMatchesRegularExpressionが存在しないため、その場合はpreg_matchを使って判定します
     * @param string $pattern 
     * @param string $string 
     * @param string $message 
     */
    private function myAssertMatchesRegularExpression(string $pattern, string $string, string $message = ''): void
    {
        if (method_exists($this, 'assertMatchesRegularExpression')) {
            $this->assertMatchesRegularExpression($pattern, $string, $message);
        } else {
            $this->assertTrue( preg_match($pattern, $string) === 1, $message );
        }
    }
}
