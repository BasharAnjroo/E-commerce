<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Currency extends Model
{


    /*
	|--------------------------------------------------------------------------
	| GLOBAL VARIABLES
	|--------------------------------------------------------------------------
	*/

    protected $table = 'currencies';
    //protected $primaryKey = 'id';
    public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = [
    	'iso',
    	'name',
    	'value',
    	'default',
        'status',
        'symbol',
        'thousands',
        'precision',
        'symbol_first',
	];
    // protected $hidden = [];
    // protected $dates = [];

    /*
	|--------------------------------------------------------------------------
	| FUNCTIONS
	|--------------------------------------------------------------------------
	*/


    public static function getDefaultCurrencyName() {
        $default_currency = Currency::where('default', 1)->first();

        if(isset($default_currency)){
            $default_currency_name = $default_currency->name;
        } else {
            $default_currency_name = '-';
        }

        return $default_currency_name;
    }


    public static function getDefaultCurrencyId() {
        $default_currency = Currency::where('default', 1)->first();

        if(isset($default_currency)){
            $default_currency_id = $default_currency->id;
        } else {
            $default_currency_id = NULL;
        }

        return $default_currency_id;
    }

    /*
	|--------------------------------------------------------------------------
	| RELATIONS
	|--------------------------------------------------------------------------
	*/
	public function specificPrice()
	{
		return $this->belongsTo('App\Models\SpecificPrice', 'id');
	}


    protected static $iso               = '';
    protected static $name              = '';
    protected static $symbol            = '';
    protected static $value             = 1;
    protected static $precision         = 0;
    protected static $symbol_first      = 0;
    protected static $thousands         = ',';
    protected static $decimal           = '.';
    protected static $list              = null;
    protected static $getArray          = null;
    protected static $getisoActive      = null;
    protected static $checkListCurrency = [];
    protected $guarded                  = [];


    public static function getListAll()
    {
        if (!self::$list) {
            self::$list = self::get()
                ->keyBy('iso');
        }
        return self::$list;
    }

    public static function getisoActive()
    {
        if (self::$getisoActive === null) {
            self::$getisoActive = self::where('status', 1)
                ->pluck('name', 'iso')
                ->all();
        }
        return self::$getisoActive;
    }


    public static function getisoAll()
    {
        if (self::$getArray === null) {
            self::$getArray = self::pluck('name', 'iso')->all();
        }
        return self::$getArray;
    }

    /**
     * [setiso description]
     * @param [type] $iso [description]
     */
    public static function setiso($iso)
    {
        self::$iso = $iso;
        if (empty(self::$checkListCurrency[$iso])) {
            self::$checkListCurrency[$iso] = self::where('iso', $iso)->first();
        }
        $checkCurrency = self::$checkListCurrency[$iso];
        if ($checkCurrency) {
            self::$name          = $checkCurrency->name;
            self::$symbol        = $checkCurrency->symbol;
            self::$value = $checkCurrency->value;
            self::$precision     = $checkCurrency->precision;
            self::$symbol_first  = $checkCurrency->symbol_first;
            self::$thousands     = $checkCurrency->thousands;
            self::$decimal       = ($checkCurrency->thousands == '.') ? ',' : '.';
        }
    }

    /**
     * [getCurrency description]
     * @return [type] [description]
     */
    public static function getCurrency()
    {
        return [
            'iso'          => self::$iso,
            'name'          => self::$name,
            'symbol'        => self::$symbol,
            'value' => self::$value,
            'precision'     => self::$precision,
            'symbol_first'  => self::$symbol_first,
            'thousands'     => self::$thousands,
            'decimal'       => self::$decimal,
        ];
    }

    /*
     * [getiso description]
     * @return [type] [description]
     */
    public static function getiso()
    {
        return self::$iso;
    }

    /**
     * [getRate description]
     * @return [type] [description]
     */
    public static function getRate()
    {
        return self::$value;
    }

    /**
     * [getValue description]
     * @param  float  $money [description]
     * @param  [type] $rate  [description]
     * @return [type]        [description]
     */
    public static function getValue(float $money, $rate = null)
    {
        if (!empty($rate)) {
            return $money * $rate;
        } else {
            return $money * self::$value;
        }

    }

    /**
     * [format description]
     * @param  float  $money [description]
     * @return [type]        [description]
     */
    public static function format(float $money)
    {
        return number_format($money, self::$precision, self::$decimal, self::$thousands);
    }

    /**
     * [render description]
     * @param  float   $money                [description]
     * @param  [type]  $currency             [description]
     * @param  [type]  $rate                 [description]
     * @param  boolean $space_between_symbol [description]
     * @param  boolean $include_symbol       [description]
     * @return [type]                        [description]
     */
    public static function render(float $money, $currency = null, $rate = null, $space_between_symbol = false, $include_symbol = true)
    {
        $space_symbol = ($space_between_symbol) ? ' ' : '';
        $dataCurrency = self::getCurrency();
        if ($currency) {
            if (empty(self::$checkListCurrency[$currency])) {
                self::$checkListCurrency[$currency] = self::where('iso', $currency)->first();
            }
            $checkCurrency = self::$checkListCurrency[$currency];
            if ($checkCurrency) {
                $dataCurrency = $checkCurrency;
            }
        }
        //Get currently value
        $value = self::getValue($money, $rate);

        $symbol = ($include_symbol) ? $dataCurrency['symbol'] : '';

        if ($dataCurrency['symbol_first']) {
            if ($money < 0) {
                return '-' . $symbol . $space_symbol . self::format(abs($value));
            } else {
                return $symbol . $space_symbol . self::format($value);
            }
        } else {
            return self::format($value) . $space_symbol . $symbol;
        }
    }

    /**
     * [onlyRender description]
     * @param  float   $money                [description]
     * @param  [type]  $currency             [description]
     * @param  boolean $space_between_symbol [description]
     * @param  boolean $include_symbol       [description]
     * @return [type]                        [description]
     */
    public static function onlyRender(float $money, $currency, $space_between_symbol = false, $include_symbol = true)
    {
        if (empty(self::$checkListCurrency[$currency])) {
            self::$checkListCurrency[$currency] = self::where('iso', $currency)->first();
        }
        $checkCurrency = self::$checkListCurrency[$currency];
        $space_symbol  = ($space_between_symbol) ? ' ' : '';
        $symbol        = ($include_symbol) ? $checkCurrency['symbol'] : '';
        if ($checkCurrency['symbol_first']) {
            if ($money < 0) {
                return '-' . $symbol . $space_symbol . self::format(abs($money));
            } else {
                return $symbol . $space_symbol . self::format($money);
            }

        } else {
            return self::format($money) . $space_symbol . $symbol;
        }
    }

    /**
     * Sum value of cart
     *
     * @param   float  $rate     [$rate description]
     *
     * @return  [array]
     */
    public static function sumCart(float $rate = null)
    {
        $carts = Cart::instance('default')->getItemsGroupByStore();
        $dataReturn = [];

        $sumSubtotal  = 0;
        $sumSubtotalWithTax  = 0;
        $rate = ($rate) ? $rate : self::$value;
        foreach ($carts as $storeId => $cart) {
            $sumSubtotalStore  = 0;
            $sumSubtotalWithTaxStore  = 0;
            foreach ($cart as $detail) {
                $sumValue = $detail->qty * self::getValue($detail->price, $rate);
                $sumValueWithTax = $detail->qty * self::getValue(sc_tax_price($detail->price, $detail->tax), $rate);
                $sumSubtotal += $sumValue;
                $sumSubtotalStore += $sumValue;
                $sumSubtotalWithTax +=  $sumValueWithTax;
                $sumSubtotalWithTaxStore+= $sumValueWithTax;
            }
            $dataReturn['store'][$storeId]['subTotal'] = $sumSubtotalStore;
            $dataReturn['store'][$storeId]['subTotalWithTax'] = $sumSubtotalWithTaxStore;

        }
        $dataReturn['subTotal'] = $sumSubtotal;
        $dataReturn['subTotalWithTax'] = $sumSubtotalWithTax;
        return $dataReturn;
    }

    public static function getListRate()
    {
        return self::pluck('value', 'iso')->all();
    }

    public static function getListActive()
    {
        return self::where('status', 1)
            ->sort()
            ->get();
    }
    //Scort
    public function scopeSort($query, $sortBy = null, $sortOrder = 'asc')
    {
        $sortBy = $sortBy ?? 'sort';
        return $query->orderBy($sortBy, $sortOrder);
    }

    protected static function boot() {
        parent::boot();
        static::deleting(function ($model) {
            if(in_array($model->id, SC_GUARD_CURRENCY)){
                return false;
            }
        });
    }
    /*
	|--------------------------------------------------------------------------
	| SCOPES
	|--------------------------------------------------------------------------
	*/

    /*
	|--------------------------------------------------------------------------
	| ACCESORS
	|--------------------------------------------------------------------------
	*/

    /*
	|--------------------------------------------------------------------------
	| MUTATORS
	|--------------------------------------------------------------------------
	*/
}
