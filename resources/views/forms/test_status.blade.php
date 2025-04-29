@php
$products = [
['id' => 1, 'gambar' => 'image1.jpg', 'payment' => 'Credit Card', 'description' => 'Product 1 description'],
['id' => 2, 'gambar' => 'image2.jpg', 'payment' => 'PayPal', 'description' => 'Product 2 description'],
// ... add more products up to 10
['id' => 10, 'gambar' => 'image10.jpg', 'payment' => 'Bank Transfer', 'description' => 'Product 10 description'],
];
@endphp

@foreach ($products as $product)
<div class="product">
    <h2>Product ID: {{ $product['id'] }}</h2>
    <img src="{{ asset('images/' . $product['gambar']) }}" alt="Product Image">
    <p>Payment Method: {{ $product['payment'] }}</p>
    <p>Description: {{ $product['description'] }}</p>
</div>
@endforeach