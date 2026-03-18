export default function Home({ featuredCamera, cameras, products }) {
    return (
        <main className="mx-auto max-w-5xl p-8">
            <h1 className="text-3xl font-semibold">SureShotSupply</h1>

            <section className="mt-8">
                <h2 className="text-xl font-medium">Featured Camera</h2>
                <p className="mt-2">
                    {featuredCamera ? featuredCamera.name : 'No featured camera available.'}
                </p>
            </section>

            <section className="mt-8">
                <h2 className="text-xl font-medium">Cameras</h2>
                <ul className="mt-2 list-disc pl-6">
                    {cameras.map((camera) => (
                        <li key={camera.id}>{camera.name}</li>
                    ))}
                </ul>
            </section>

            <section className="mt-8">
                <h2 className="text-xl font-medium">Products</h2>
                <ul className="mt-2 list-disc pl-6">
                    {products.map((product) => (
                        <li key={product.id}>{product.name}</li>
                    ))}
                </ul>
            </section>
        </main>
    );
}
