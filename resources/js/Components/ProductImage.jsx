export default function ProductImage({ src, alt, className = '' }) {
    if (src) {
        return (
            <img
                src={src}
                alt={alt}
                className={`h-full w-full object-cover ${className}`}
            />
        );
    }

    return (
        <div className={`flex items-center justify-center bg-site-card ${className}`}>
            <svg
                viewBox="0 0 64 64"
                aria-hidden="true"
                className="h-14 w-14 text-[#2a2a2a]"
                fill="none"
                stroke="currentColor"
                strokeWidth="1.5"
            >
                <rect x="12" y="20" width="40" height="26" rx="4" />
                <path d="M22 20l4-6h12l4 6" />
                <circle cx="32" cy="33" r="8" />
                <circle cx="46" cy="26" r="1.5" fill="currentColor" stroke="none" />
            </svg>
        </div>
    );
}
