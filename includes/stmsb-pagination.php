<?php

class STMSB_Pagination
{
    public $current_page;
    public $per_page;
    public $total_count;

    public function __construct($page = 1, $per_page = 10, $total_count = 0)
    {
        $this->current_page = (int)$page;
        $this->per_page     = (int)$per_page;
        $this->total_count  = (int)$total_count;
    }

    public function stmsb_offset(){
        return ($this->current_page - 1) * $this->per_page;
    }

    public function stmsb_total_page()
    {
        return ceil($this->total_count / $this->per_page);
    }

    public function stmsb_previous_page()
    {
        return $this->current_page-1;
    }

    public function stmsb_next_page()
    {
        return $this->current_page+1;
    }

    public function stmsb_has_previous_page()
    {
        return $this->stmsb_previous_page() >= 1 ? true : false;
    }

    public function stmsb_has_next_page()
    {
        return $this->stmsb_next_page() <= $this->stmsb_total_page() ? true : false;
    }
}