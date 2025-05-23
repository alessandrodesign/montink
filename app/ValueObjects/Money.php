<?php

namespace App\ValueObjects;

use NumberFormatter;
use InvalidArgumentException;

class Money
{
    private float $amount;
    private string $currency;
    private string $locale;
    private const string USD = 'USD';
    private const string BRL = 'BRL';
    private const string EUR = 'EUR';
    private static array $exchangeRates = [
        self::USD => 1.0,
        self::BRL => 5.25,
        self::EUR => 0.92,
    ];

    /**
     * Construtor
     *
     * @param float|int|string $amount Valor em unidades (ex: 10.50)
     * @param string|null $currency Código da moeda (ex: 'USD', 'BRL')
     * @param string|null $locale Localidade para formatação (ex: 'en_US', 'pt_BR')
     */
    public function __construct(float|int|string $amount, ?string $currency = self::BRL, ?string $locale = null)
    {
        $this->amount = floatval($amount);
        $this->locale = $locale ?? str_replace('-', '_', service('request')->getLocale());
        $this->currency = $currency ? strtoupper($currency) : $this->findCurrency();
    }

    private function findCurrency(): string
    {
        return match ($this->locale) {
            'es' => self::EUR,
            'en' => self::USD,
            default => self::BRL,
        };
    }

    /**
     * Formata o valor usando NumberFormatter
     *
     * @return string
     */
    public function format(): string
    {
        $formatter = new NumberFormatter($this->locale, NumberFormatter::CURRENCY);

        $formatted = $formatter->formatCurrency($this->amount, $this->currency);

        if ($formatted === false) {
            throw new InvalidArgumentException("Erro ao formatar o valor monetário.");
        }

        return $formatted;
    }

    /**
     * Converte para outra moeda
     *
     * @param string $toCurrency Código da moeda destino
     * @param string|null $toLocale Localidade para formatação da moeda destino
     * @return Money Novo objeto Money convertido
     */
    public function convertTo(string $toCurrency, ?string $toLocale = null): Money
    {
        $toCurrency = strtoupper($toCurrency);

        if (!isset(self::$exchangeRates[$this->currency]) || !isset(self::$exchangeRates[$toCurrency])) {
            throw new InvalidArgumentException("Conversão de moeda não suportada: {$this->currency} para {$toCurrency}");
        }

        // Converte para USD como base, depois para destino
        $amountInUSD = $this->amount / self::$exchangeRates[$this->currency];
        $convertedAmount = $amountInUSD * self::$exchangeRates[$toCurrency];

        return new Money($convertedAmount, $toCurrency, $toLocale ?? $this->locale);
    }

    /**
     * Retorna o valor numérico
     *
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * Retorna o código da moeda
     *
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * Retorna a localidade
     *
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * Retorna string formatada ao converter para string
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->format();
    }
}