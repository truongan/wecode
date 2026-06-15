<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
	//
	protected $fillable = ["key", "value"];

	/**
	 * Request-level cache of every setting as a key => value map.
	 *
	 * @var array<string, string>|null
	 */
	protected static ?array $cache = null;

	/**
	 * Load every setting once per request and reuse it for subsequent lookups.
	 *
	 * @return array<string, string>
	 */
	protected static function cache(): array
	{
		if (static::$cache === null) {
			static::$cache = static::pluck("value", "key")->all();
		}

		return static::$cache;
	}

	public static function get($key, $default = null)
	{
		return static::cache()[$key] ?? $default;
	}

	public static function set($key, $value)
	{
		$a = Setting::where("key", $key)->first();
		if ($a != null) {
			$a->value = $value;
			$a->save();
			if (static::$cache !== null) {
				static::$cache[$key] = $value;
			}

			return true;
		}

		return false;
	}

	public static function load_all()
	{
		return static::cache();
	}

	public static function find_by_key($key)
	{
		return Setting::where("key", $key)->first();
	}
}
