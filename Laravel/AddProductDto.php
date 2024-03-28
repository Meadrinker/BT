<?php


class AddProductDto {

    private int $producerId;
    private string $code;
    private int $weight;
    private ?string $description;
    private ?int $ean;

    /**
     * @param int $producerId
     * @param string $code
     * @param int $weight
     * @param string|null $description
     * @param int|null $ean
     */
    public function __construct(int $producerId, string $code, int $weight, ?string $description, ?int $ean) {
        $this->producerId = $producerId;
        $this->code = $code;
        $this->weight = $weight;
        $this->description = $description;
        $this->ean = $ean;
    }

    /**
     * @return int
     */
    public function getProducerId(): int {
        return $this->producerId;
    }

    /**
     * @return string
     */
    public function getCode(): string {
        return $this->code;
    }

    /**
     * @return int
     */
    public function getWeight(): int {
        return $this->weight;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string {
        return $this->description;
    }

    /**
     * @return int|null
     */
    public function getEan(): ?int {
        return $this->ean;
    }

}
