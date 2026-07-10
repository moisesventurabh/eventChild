<?php

namespace App\Contracts;

interface WeatherProviderInterface
{
    /**
     * Obtém os dados climáticos atuais e previsões com base nas coordenadas geográficas.
     * @param float $latitude
     * @param float $longitude
     * @return array Dto/Array padronizado com as métricas climáticas necessárias
     */
    public function getWeatherCoordinates(float $latitude, float $longitude): array;
}