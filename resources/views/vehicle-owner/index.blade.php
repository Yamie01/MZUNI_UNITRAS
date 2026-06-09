@extends('layouts.vehicle-owner')

@section('title','My Vehicles')

@section('content')

<div class="container">

    <h1 class="mb-4">My Vehicles</h1>

    <a href="{{ route('vehicle-owner.vehicles.create') }}" class="btn btn-primary mb-3">
        Add New Vehicle
    </a>

    <table class="table table-bordered table-striped">

        <thead class="table-dark">
            <tr>
                <th>Reg No.</th>
                <th>Model</th>
                <th>Type</th>
                <th>Capacity</th>
                <th>Status</th>
                <th>Approved</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>

        @forelse($vehicles as $vehicle)

            <tr>

                <td>{{ $vehicle->registration_number }}</td>

                <td>{{ $vehicle->model }}</td>

                <td>{{ $vehicle->vehicle_type }}</td>

                <td>{{ $vehicle->capacity }}</td>

                <td>{{ $vehicle->status }}</td>

                <td>
                    @if($vehicle->is_approved)
                        <span class="badge bg-success">Approved</span>
                    @else
                        <span class="badge bg-warning text-dark">Pending</span>
                    @endif
                </td>

                <td>

                    <a href="{{ route('vehicle-owner.vehicles.edit', $vehicle->id) }}"
                       class="btn btn-sm btn-primary">
                       Edit
                    </a>

                    <form action="{{ route('vehicle-owner.vehicles.destroy', $vehicle->id) }}"
                          method="POST"
                          class="d-inline">

                        @csrf
                        @method('DELETE')

                        <button class="btn btn-sm btn-danger"
                                onclick="return confirm('Delete vehicle?')">
                            Delete
                        </button>

                    </form>

                </td>

            </tr>

        @empty

            <tr>
                <td colspan="7" class="text-center">No vehicles found</td>
            </tr>

        @endforelse

        </tbody>

    </table>

    {{ $vehicles->links() }}

</div>

@endsection