<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label><strong>Movie Title:</strong></label>
            <p>{{ $report->movie_title }}</p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label><strong>Issue Type:</strong></label>
            <p><span class="badge badge-info">{{ $report->issue_type_label }}</span></p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label><strong>Description:</strong></label>
            <p>{{ $report->description }}</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label><strong>Email:</strong></label>
            <p>
                @if($report->email)
                    <a href="mailto:{{ $report->email }}">{{ $report->email }}</a>
                @else
                    <span class="text-muted">Not provided</span>
                @endif
            </p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label><strong>Status:</strong></label>
            <p><span class="badge badge-{{ $report->status_color }}">{{ $report->status_label }}</span></p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label><strong>Reported Date:</strong></label>
            <p>{{ $report->created_at->format('M d, Y H:i:s') }}</p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label><strong>Last Updated:</strong></label>
            <p>{{ $report->updated_at->format('M d, Y H:i:s') }}</p>
        </div>
    </div>
</div>

@if($report->admin_notes)
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label><strong>Admin Notes:</strong></label>
                <p>{{ $report->admin_notes }}</p>
            </div>
        </div>
    </div>
@endif

@if($report->movie)
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label><strong>Movie Details:</strong></label>
                <p>
                    <a href="{{ route('movie.show', $report->movie->slug) }}" target="_blank" class="btn btn-sm btn-primary">
                        <i class="fas fa-external-link-alt"></i> View Movie
                    </a>
                    @if($report->movie->tmdb_id)
                        <a href="https://www.themoviedb.org/movie/{{ $report->movie->tmdb_id }}" target="_blank" class="btn btn-sm btn-info">
                            <i class="fas fa-external-link-alt"></i> View on TMDB
                        </a>
                    @endif
                </p>
            </div>
        </div>
    </div>
@endif

@if($report->status !== 'resolved')
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label><strong>Update Status:</strong></label>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-warning" onclick="updateReportStatus({{ $report->id }}, 'reviewed')">
                        <i class="fas fa-eye"></i> Mark as Reviewed
                    </button>
                    <button type="button" class="btn btn-sm btn-success" onclick="updateReportStatus({{ $report->id }}, 'resolved')">
                        <i class="fas fa-check"></i> Mark as Resolved
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

<script>
    function updateReportStatus(reportId, status) {
        const notes = prompt('Add admin notes (optional):');
        
        fetch(`/admin/reports/${reportId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                status: status,
                admin_notes: notes || null
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating report status: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating report status. Please try again.');
        });
    }
</script>
