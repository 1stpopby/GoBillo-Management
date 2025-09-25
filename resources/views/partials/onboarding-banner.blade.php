@if($onboardingData && $onboardingData['show'])
<div class="onboarding-banner bg-white rounded-lg shadow-lg mb-6" id="onboardingBanner" style="border: 2px solid #e3f2fd; padding: 2.5rem;">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <h4 class="mb-3 text-primary">
                <i class="bi bi-rocket-takeoff me-2"></i>
                Welcome to ProMax Team! Let's get you started
            </h4>
            <p class="mb-3 text-secondary">Complete these steps to set up your construction management system:</p>
            
            <!-- Progress Bar -->
            <div class="progress mb-4" style="height: 30px; background-color: #f0f0f0;">
                <div class="progress-bar" 
                     role="progressbar" 
                     style="width: {{ $onboardingData['completionPercentage'] }}%; background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);"
                     aria-valuenow="{{ $onboardingData['completionPercentage'] }}" 
                     aria-valuemin="0" 
                     aria-valuemax="100">
                    <span class="h6 text-white">{{ $onboardingData['progress']->completed_steps }} of {{ $onboardingData['progress']->total_steps }} steps complete ({{ $onboardingData['completionPercentage'] }}%)</span>
                </div>
            </div>
            
            <!-- Steps Grid with Colorful Badges -->
            <div class="row g-2">
                @foreach($onboardingData['steps'] as $index => $step)
                    @php
                        $isCompleted = $onboardingData['progress']->isStepCompleted($step->key);
                        $isCurrent = $onboardingData['progress']->current_step === $step->key;
                        
                        // Define colors for each step
                        $colors = [
                            '#28a745', // Green
                            '#17a2b8', // Cyan
                            '#ffc107', // Amber
                            '#6f42c1', // Purple
                            '#e83e8c', // Pink
                            '#fd7e14', // Orange
                        ];
                        $stepColor = $colors[$index % count($colors)];
                    @endphp
                    <div class="col-md-4 col-sm-6">
                        <div class="d-flex align-items-center p-2 rounded-3 {{ $isCompleted ? 'bg-success bg-opacity-10 border border-success' : ($isCurrent ? 'bg-warning bg-opacity-10 border border-warning border-2' : 'bg-light') }}">
                            <div class="me-2">
                                @if($isCompleted)
                                    <div class="rounded-circle p-2 bg-success text-white" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-check-lg fs-6"></i>
                                    </div>
                                @elseif($isCurrent)
                                    <div class="rounded-circle p-2 text-white" style="background-color: {{ $stepColor }}; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-arrow-right fs-6"></i>
                                    </div>
                                @else
                                    <div class="rounded-circle p-2 border border-2" style="border-color: {{ $stepColor }} !important; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                        <span style="color: {{ $stepColor }}; font-weight: bold;">{{ $index + 1 }}</span>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <small class="d-block {{ $isCompleted ? 'text-success fw-bold' : ($isCurrent ? 'text-dark fw-bold' : 'text-muted') }}">
                                    {{ $step->name }}
                                </small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if($onboardingData['nextAction'])
            <div class="mt-4">
                <div class="alert alert-info border-0" style="background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);">
                    <strong><i class="bi bi-lightbulb me-2"></i>Next Step:</strong> {{ $onboardingData['nextAction']['text'] }}
                </div>
            </div>
            @endif
        </div>
        
        <div class="col-lg-4 text-end">
            @if($onboardingData['completionPercentage'] < 100)
                @if($onboardingData['nextAction'])
                    <a href="{{ route($onboardingData['nextAction']['route']) }}" class="btn btn-lg mb-3 d-block text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="{{ $onboardingData['nextAction']['icon'] }} me-2"></i>
                        {{ $onboardingData['nextAction']['text'] }}
                    </a>
                @endif
                
                <div class="btn-group d-flex mb-3" role="group">
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="dismissOnboarding()">
                        <i class="bi bi-clock me-1"></i>Later
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="skipOnboarding()">
                        <i class="bi bi-x-lg me-1"></i>Skip
                    </button>
                </div>
                
                <a href="{{ route('kb.index') }}" class="btn btn-link text-decoration-none">
                    <i class="bi bi-book me-1"></i>View Full KB
                </a>
            @else
                <div class="text-center">
                    <i class="bi bi-trophy-fill text-warning" style="font-size: 4rem;"></i>
                    <h5 class="text-success mt-2">Setup Complete!</h5>
                    <p class="text-muted">You're all set to manage your projects.</p>
                    <button type="button" class="btn btn-success btn-lg" onclick="completeOnboarding()">
                        <i class="bi bi-check-circle me-2"></i>Close Setup Guide
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.onboarding-banner {
    transition: all 0.3s ease;
}
.onboarding-banner:hover {
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
}
.progress-bar {
    animation: progressAnimation 2s ease;
}
@keyframes progressAnimation {
    from {
        width: 0;
    }
}
</style>

<script>
function dismissOnboarding() {
    fetch('{{ route("onboarding.dismiss") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('onboardingBanner').style.display = 'none';
        }
    })
    .catch(error => console.error('Error:', error));
}

function skipOnboarding() {
    if (confirm('Are you sure you want to skip the setup guide? You can access help anytime from the Knowledge Base.')) {
        fetch('{{ route("onboarding.skip") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('onboardingBanner').style.display = 'none';
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

function completeOnboarding() {
    fetch('{{ route("onboarding.complete") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('onboardingBanner').style.display = 'none';
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>
@endif