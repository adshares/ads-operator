<?php

namespace Adshares\AdsOperator\Document\Transaction;

use Adshares\Ads\Entity\Transaction\SendManyTransaction as BaseSendManyTransaction;
use Adshares\Ads\Entity\Transaction\SendManyTransactionWire;
use Adshares\AdsOperator\Document\ArrayableInterface;

class SendManyTransaction extends BaseSendManyTransaction implements ArrayableInterface
{
    public function toArray(): array
    {
        return [
            "id" => $this->id,
            "size" => $this->size,
            "type" => $this->type,
            "blockId" => $this->blockId,
            "messageId" => $this->messageId,
            "msgId" => $this->msgId,
            "node" => $this->node,
            "senderAddress" => $this->senderAddress,
            "senderFee" => $this->senderFee,
            "signature" => $this->signature,
            "transactionCount" => $this->transactionCount,
            "time" => $this->time,
            "user" => $this->user,
            "wires" => $this->transformTransactionWiresToArray($this->wires),
        ];
    }

    private function transformTransactionWiresToArray(array $wires): array
    {
        $transformed = [];

        /** @var SendManyTransactionWire $transaction */
        foreach ($wires as $transaction) {
            $transformed[] = [
                "amount" => $transaction->getAmount(),
                "targetAddress" => $transaction->getTargetAddress(),
                "targetNode" => $transaction->getTargetNode(),
                "targetUser" => $transaction->getTargetUser(),
            ];
        }

        return $transformed;
    }
}
