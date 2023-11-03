<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    //define properti
    public $status;
    public $message;
    public $status_code;

    /**
     * __construct
     *
     * @param  mixed $status
     * @param  mixed $message
     * @param  mixed $resource
     * @return void
     */
    public function __construct($status, $message, $status_code, $resource)
    {
        parent::__construct($resource);
        $this->status  = $status;
        $this->message  = $message;
        $this->status_code = $status_code;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'success'   => $this->status,
            'message'   => $this->message,
            'status'    => $this->status_code,
            'data'      => $this->resource
        ];
    }
}
