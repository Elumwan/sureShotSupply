export default function MediaPlaceholder({ label, className = '' }) {
    return (
        <div
            className={`relative overflow-hidden border border-site-border bg-[linear-gradient(135deg,#171717_0%,#101010_55%,#1f1f1f_100%)] ${className}`}
        >
            <div className="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(196,146,74,0.12),transparent_28%),linear-gradient(180deg,transparent,rgba(0,0,0,0.45))]" />
            <div className="absolute inset-x-0 bottom-0 flex items-center justify-between px-5 py-4 text-[10px] uppercase tracking-[0.22em] text-site-text-ghost">
                <span>{label}</span>
                <span>Preview pending</span>
            </div>
        </div>
    );
}
