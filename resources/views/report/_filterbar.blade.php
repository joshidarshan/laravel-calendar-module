<div class="filter-bar d-flex justify-content-end mb-3">

    <!-- TOP RIGHT : DAY / WEEK / MONTH / ALL -->
    <div class="btn-group">
        <a href="{{ route('report.day.table') }}"
           class="btn {{ request()->is('report/day/*') ? 'btn-primary' : 'btn-outline-primary' }}">
            Day
        </a>

        <a href="{{ route('report.week.table') }}"
           class="btn {{ request()->is('report/week/*') ? 'btn-primary' : 'btn-outline-primary' }}">
            Week
        </a>

        <a href="{{ route('report.month.table') }}"
           class="btn {{ request()->is('report/month/*') ? 'btn-primary' : 'btn-outline-primary' }}">
            Month
        </a>

        <a href="{{ route('report.all.table') }}"
           class="btn {{ request()->is('report/all/*') ? 'btn-primary' : 'btn-outline-secondary' }}">
            All
        </a>
    </div>

</div>
