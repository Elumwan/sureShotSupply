import ProductCard from '../Components/ProductCard';
import SectionHeader from '../Components/SectionHeader';
import SiteLayout from '../Layouts/SiteLayout';

function Shop({ products }) {
    return (
        <main className="mx-auto max-w-7xl px-6 py-16 lg:px-10 lg:py-20">
            <SectionHeader
                eyebrow="Always in stock"
                title="Accessories"
                description="Straps and small carry essentials that stay close without getting loud."
            />

            <div className="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                {products.map((product) => (
                    <ProductCard key={product.id} product={product} showButton />
                ))}
            </div>
        </main>
    );
}

Shop.layout = (page) => <SiteLayout>{page}</SiteLayout>;

export default Shop;
