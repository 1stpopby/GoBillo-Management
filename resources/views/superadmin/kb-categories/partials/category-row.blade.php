<tr>
    <td>
        @if($level > 0)
            <span class="text-muted ms-{{ $level * 3 }}">└─</span>
        @endif
        @if($category->icon)
            <i class="bi bi-{{ $category->icon }} me-2"></i>
        @endif
        {{ $category->name }}
    </td>
    <td><code>{{ $category->slug }}</code></td>
    <td>
        <span class="badge bg-secondary">{{ $category->articles_count ?? 0 }}</span>
    </td>
    <td>
        @if($category->is_active)
            <span class="badge bg-success">Active</span>
        @else
            <span class="badge bg-warning">Inactive</span>
        @endif
    </td>
    <td>
        <div class="btn-group btn-group-sm" role="group">
            <a href="{{ route('superadmin.kb.categories.edit', $category) }}" class="btn btn-outline-primary" title="Edit">
                <i class="bi bi-pencil"></i>
            </a>
            <form id="delete-form-{{ $category->id }}" 
                  action="{{ route('superadmin.kb.categories.destroy', $category) }}" 
                  method="POST" 
                  class="d-inline">
                @csrf
                @method('DELETE')
                <button type="button" onclick="deleteCategory({{ $category->id }})" class="btn btn-outline-danger" title="Delete">
                    <i class="bi bi-trash"></i>
                </button>
            </form>
        </div>
    </td>
</tr>

@if(!empty($category->children))
    @foreach($category->children as $child)
        @include('superadmin.kb-categories.partials.category-row', ['category' => $child, 'level' => $level + 1])
    @endforeach
@endif