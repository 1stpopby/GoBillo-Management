@if($onboardingData && $onboardingData['show'])
<div class="onboarding-banner rounded-lg shadow-lg mb-6 p-6" id="onboardingBanner" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <h4 class="mb-3 text-white">
                <i class="bi bi-rocket-takeoff me-2"></i>
                Welcome to ProMax Team! Let's get you started
            </h4>
            <p class="mb-3 text-white">Complete these steps to set up your construction management system:</p>
            
            <!-- Progress Bar -->
            <div class="progress mb-3" style="height: 30px; background-color: rgba(255,255,255,0.2);">
                <div class="progress-bar bg-success" 
                     role="progressbar" 
                     style="width: {{ $onboardingData['completionPercentage'] }}%;"
                     aria-valuenow="{{ $onboardingData['completionPercentage'] }}" 
                     aria-valuemin="0" 
                     aria-valuemax="100">
                    <span class="h6">{{ $onboardingData['progress']->completed_steps }} of {{ $onboardingData['progress']->total_steps }} steps complete ({{ $onboardingData['completionPercentage'] }}%)</span>
                </div>
            </div>
            
            <!-- Steps Grid -->
            <div class="row g-2">
                @foreach($onboardingData['steps'] as $step)
                    @php
                        $isCompleted = $onboardingData['progress']->isStepCompleted($step->key);
                        $isCurrent = $onboardingData['progress']->current_step === $step->key;
                    @endphp
                    <div class="col-md-4 col-sm-6">
                        <div class="d-flex align-items-center p-2 rounded {{ $isCompleted ? 'bg-success bg-opacity-25' : ($isCurrent ? 'bg-warning bg-opacity-25 border border-warning' : 'bg-white bg-opacity-10') }}">
                            <div class="me-2">
                                @if($isCompleted)
                                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                @elseif($isCurrent)
                                    <i class="bi bi-arrow-right-circle-fill text-warning fs-5"></i>
                                @else
                                    <i class="bi bi-circle fs-5 text-white-50"></i>
                                @endif
                            </div>
                            <div>
                                <small class="d-block {{ $isCompleted ? 'text-success fw-bold' : 'text-white' }}">
                                    {{ $step->name }}
                                </small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if($onboardingData['nextAction'])
            <div class="mt-4">
                <p class="mb-2">
                    <strong>Next Step:</strong> {{ $onboardingData['nextAction']['text'] }}
                </p>
            </div>
            @endif
        </div>
        
        <div class="col-lg-4 text-end">
            @if($onboardingData['completionPercentage'] < 100)
                @if($onboardingData['nextAction'])
                    <a href="{{ route($onboardingData['nextAction']['route']) }}" class="btn btn-light btn-lg mb-2 d-block">
                        <i class="{{ $onboardingData['nextAction']['icon'] }} me-2"></i>
                        {{ $onboardingData['nextAction']['text'] }}
                    </a>
                @endif
                
                <div class="btn-group d-flex" role="group">
                    <button type="button" class="btn btn-outline-light btn-sm" onclick="dismissOnboarding()">
                        <i class="bi bi-clock me-1"></i>Remind Later
                    </button>
                    <button type="button" class="btn btn-outline-light btn-sm" onclick="skipOnboarding()">
                        <i class="bi bi-x-lg me-1"></i>Skip Setup
                    </button>
                </div>
                
                <a href="{{ route('knowledge-base.index') }}" class="btn btn-link text-white mt-2">
                    <i class="bi bi-book me-1"></i>View Full KB
                </a>
            @else
                <div class="text-center">
                    <i class="bi bi-trophy-fill text-warning" style="font-size: 4rem;"></i>
                    <h5 class="text-white mt-2">Setup Complete!</h5>
                    <p class="text-white-50">You're all set to manage your projects.</p>
                    <button type="button" class="btn btn-success btn-lg" onclick="completeOnboarding()">
                        <i class="bi bi-check-circle me-2"></i>Close Setup Guide
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

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