@extends('layouts.app')

@section('title', 'Contact - Mzuni UNITRAS')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-envelope me-2"></i>Contact Us</h4>
                </div>
                <div class="card-body">
                    <h3>Get in Touch</h3>
                    <p>For inquiries, support, or feedback, please contact us:</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h5>Contact Information</h5>
                            <p><i class="fas fa-map-marker-alt text-primary me-2"></i> Mzuzu University, Malawi</p>
                            <p><i class="fas fa-phone text-primary me-2"></i> +265 123 456 789</p>
                            <p><i class="fas fa-envelope text-primary me-2"></i> unitras@mzuni.ac.mw</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Office Hours</h5>
                            <p>Monday - Friday: 8:00 AM - 5:00 PM</p>
                            <p>Saturday: 9:00 AM - 1:00 PM</p>
                            <p>Sunday: Closed</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection