<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<style>
.filter-group, .view-group {
    padding-left: 20px;
    padding-top: 14px;
}
</style>

<div class="d-flex justify-content-between align-items-center mb-3">

    <div class="btn-group filter-group">
        <button class="btn btn-primary filter-btn active" data-filter="day">Day</button>
        <button class="btn btn-outline-primary filter-btn" data-filter="week">Week</button>
        <button class="btn btn-outline-primary filter-btn" data-filter="month">Month</button>
        <button class="btn btn-outline-secondary filter-btn" data-filter="all">All</button>
    </div>

    <div class="btn-group view-group">
        <a href="#" class="btn btn-outline-dark" id="btnTable">
            <i class="bi bi-table"></i>
        </a>
        <a href="#" class="btn btn-outline-dark" id="btnChart">
            <i class="bi bi-bar-chart"></i>
        </a>
           <a href="#" class="btn btn-outline-dark">
            <i class="bi bi-download"></i>
        </a>
    </div>
 
</div>