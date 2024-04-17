<?php

declare(strict_types=1);

namespace yutayamane;

class Ulid {

	/** @var array */
	private $ulid_bytes;

	private const ULID_CHARS = '0123456789ABCDEFGHJKMNPQRSTVWXYZ';

	public function __construct( ?array $ulid_bytes = null ) {

		if ( is_null( $ulid_bytes ) ) {
			// 下位10バイトのランダム値に相当する配列を作成
			$ulid_bytes = unpack( 'C*', random_bytes( 10 ) );

			// ULIDの上位6バイトに現在の時間を格納
			$time = (int) ( microtime( true ) * 1000 );
			for ( $i = 5; $i >= 0; $i-- ) {
				array_unshift( $ulid_bytes, $time & 0xff );
				$time >>= 8;
			}
		}

		$this->ulid_bytes = $ulid_bytes;
	}

	public function __toString(): string {
		return $this->toString();
	}

	/**
	 * @return string ULID format.
	 */
	public function toString(): string {
		$bytes = $this->ulid_bytes;

		$result      = '';
		$val         = 0;
		$remain_bits = 0;
		for ( $i = count( $bytes ) - 1; $i >= 0; $i-- ) {
			$val         += $bytes[ $i ] << $remain_bits;
			$remain_bits += 8;
			while ( $remain_bits >= 5 ) {
				$result       = self::ULID_CHARS[ $val & 0x1f ] . $result;
				$remain_bits -= 5;
				$val        >>= 5;
			}
		}
		if ( $remain_bits > 0 ) {
			$result = self::ULID_CHARS[ $val & 0x1f ] . $result;
		}

		return $result;
	}

	/**
	 * @return string Hex format.
	 */
	public function toHex(): string {
		return bin2hex( implode( '', array_map( 'chr', $this->ulid_bytes ) ) );
	}

	/**
	 * @return string UUID format.
	 */
	public function toUuid(): string {
		$hex = str_pad( $this->toHex(), 32, '0', STR_PAD_LEFT );
		return substr( $hex, 0, 8 ) . '-' . substr( $hex, 8, 4 ) . '-' . substr( $hex, 12, 4 ) . '-' . substr( $hex, 16, 4 ) . '-' . substr( $hex, 20 );
	}

	/**
	 * @return float Unix time.
	 */
	public function getTime(): float {
		$val = 0;
		for ( $i = 0; $i < 6; $i++ ) {
			$val <<= 8;
			$val  += $this->ulid_bytes[ $i ];
		}
		return $val / 1000;
	}

	/**
	 * @param string $value ULID, UUID or Hex format.
	 * @return Ulid
	 */
	public static function from( string $value ): self {
		switch ( strlen( $value ) ) {
			case 26:
				return self::fromUlid( $value );
			case 36:
				return self::fromUuid( $value );
			default:
				return self::fromHex( $value );
		}
	}

	private static function fromUlid( string $value ): self {

		$chars = str_split( strtoupper( $value ) );
		$bytes = array();
		$val   = 0;
		$bits  = 0;
		for ( $i = 25; $i >= 0; $i-- ) {
			$char = $chars[ $i ];
			$idx  = strpos( self::ULID_CHARS, $char );
			if ( false === $idx ) {
				throw new \Exception( 'Invalid ULID format.' );
			}
			$val  += $idx << $bits;
			$bits += 5;
			if ( $bits >= 8 ) {
				array_unshift( $bytes, $val & 0xff );
				$val >>= 8;
				$bits -= 8;
			}
		}

		// ULIDで扱えない範囲の値が指定された場合はエラー
		if($val > 0) {
			throw new \Exception( 'Invalid ULID format.' );
		}

		return new self( $bytes );
	}

	private static function fromUuid( string $value ): self {
		if( ! preg_match( '/^([0-9a-f]{8})-([0-9a-f]{4})-([0-9a-f]{4})-([0-9a-f]{4})-([0-9a-f]{12})$/', strtolower($value)) ) {
			throw new \Exception( 'Invalid UUID format.' );
		}
		return self::fromHex( str_replace( '-', '', $value ) );
	}

	private static function fromHex( string $value ): self {
		// `0x`が付いている場合は削除
		if ( 0 === strpos( $value, '0x' ) ) {
			$value = substr( $value, 2 );
		}
		// 32文字未満の場合は32文字になるように0埋め
		$value = str_pad( $value, 32, '0', STR_PAD_LEFT );

		// フォーマットチェック
		if ( ! preg_match( '/^[0-9a-f]{32}$/', strtolower($value) ) ) {
			throw new \Exception( 'Invalid hex format.' );
		}
		return new self( array_map( 'ord', str_split( hex2bin( $value ) ) ) );
	}

}
