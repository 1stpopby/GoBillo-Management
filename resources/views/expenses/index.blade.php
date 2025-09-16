@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Expenses</h1>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('expenses.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Description, vendor, number" />
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        @foreach(['draft','submitted','approved','reimbursed'] as $st)
                            <option value="{{ $st }}" @selected(request('status')===$st)>{{ ucfirst($st) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select">
                        <option value="">All</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->name }}" @selected(request('category')===$cat->name)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Site</label>
                    <select id="filter_site_id" name="site_id" class="form-select">
                        <option value="">All</option>
                        @foreach(($sites ?? collect()) as $s)
                            <option value="{{ $s->id }}" @selected((string)request('site_id')===(string)$s->id)>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Project</label>
                    <select id="filter_project_id" name="project_id" class="form-select">
                        <option value="">All</option>
                        @foreach($projects as $proj)
                            <option value="{{ $proj->id }}" data-site="{{ $proj->site_id }}" @selected((string)request('project_id')===(string)$proj->id)>{{ $proj->name }}</option>
                        @endforeach
                    </select>
                </div>
                @if(isset($users) && $users->count())
                <div class="col-md-2">
                    <label class="form-label">User</label>
                    <select name="user_id" class="form-select">
                        <option value="">All</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" @selected((string)request('user_id')===(string)$u->id)>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="col-md-2">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control" />
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control" />
                </div>
                <div class="col-md-2 align-self-end">
                    <button class="btn btn-outline-secondary w-100" type="submit">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Site / Project</th>
                        <th>User</th>
                        <th class="text-end">Amount</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                        <tr>
                            <td>{{ $expense->expense_number ?? $expense->id }}</td>
                            <td>{{ $expense->category }}</td>
                            <td class="text-truncate" style="max-width: 260px">{{ $expense->description }}</td>
                            <td>
                                @if($expense->project)
                                    <div class="small text-muted">{{ optional($expense->project->site)->name ?? 'No Site' }}</div>
                                    <div>{{ $expense->project->name }}</div>
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ optional($expense->user)->name ?: '—' }}</td>
                            <td class="text-end">${{ number_format(($expense->amount + (($expense->mileage ?? 0) * ($expense->mileage_rate ?? 0))), 2) }}</td>
                            <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('Y-m-d') }}</td>
                            <td><span class="badge bg-light text-dark text-capitalize">{{ $expense->status ?? 'draft' }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('expenses.show', $expense) }}" class="btn btn-sm btn-outline-secondary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">No expenses found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($expenses, 'links'))
            <div class="card-footer">
                {{ $expenses->withQueryString()->links() }}
            </div>
        @endif
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const siteSelect = document.getElementById('filter_site_id');
        const projectSelect = document.getElementById('filter_project_id');
        function filterProjectsBySite() {
            const siteId = siteSelect.value;
            Array.from(projectSelect.options).forEach(opt => {
                if (!opt.value) return;
                const match = !siteId || opt.getAttribute('data-site') === siteId;
                opt.hidden = !match;
            });
            const selected = projectSelect.selectedOptions[0];
            if (selected && selected.hidden) {
                projectSelect.value = '';
            }
        }
        siteSelect?.addEventListener('change', filterProjectsBySite);
        filterProjectsBySite();
    });
    </script>
@endsection


