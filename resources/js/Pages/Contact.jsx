import { useForm, usePage } from '@inertiajs/react';

import SectionHeader from '../Components/SectionHeader';
import SiteLayout from '../Layouts/SiteLayout';

function Contact() {
    const { flash = {} } = usePage().props;
    const form = useForm({
        name: '',
        email: '',
        message: '',
    });

    function submit(event) {
        event.preventDefault();
        form.post('/contact');
    }

    return (
        <main className="mx-auto max-w-4xl px-6 py-16 lg:px-10 lg:py-20">
            <SectionHeader
                eyebrow="Contact"
                title="Get in touch"
                description="Questions about a listing, sourcing requests, or something specific you are chasing are all welcome."
            />

            <div className="panel-surface mt-10 p-8 sm:p-10">
                {flash.success ? (
                    <div className="mb-8 border border-site-amber/30 bg-site-amber/5 px-4 py-3 text-sm text-site-text-muted">
                        {flash.success}
                    </div>
                ) : null}

                <form onSubmit={submit} className="space-y-6">
                    <div>
                        <label className="mb-2 block text-[10px] uppercase tracking-[0.2em] text-site-text-faint">
                            Name
                        </label>
                        <input
                            type="text"
                            value={form.data.name}
                            onChange={(event) => form.setData('name', event.target.value)}
                            className="w-full border border-site-border bg-site-card-alt px-4 py-3 text-site-text outline-none transition focus:border-site-amber"
                        />
                        {form.errors.name ? <p className="mt-2 text-sm text-site-amber">{form.errors.name}</p> : null}
                    </div>

                    <div>
                        <label className="mb-2 block text-[10px] uppercase tracking-[0.2em] text-site-text-faint">
                            Email
                        </label>
                        <input
                            type="email"
                            value={form.data.email}
                            onChange={(event) => form.setData('email', event.target.value)}
                            className="w-full border border-site-border bg-site-card-alt px-4 py-3 text-site-text outline-none transition focus:border-site-amber"
                        />
                        {form.errors.email ? <p className="mt-2 text-sm text-site-amber">{form.errors.email}</p> : null}
                    </div>

                    <div>
                        <label className="mb-2 block text-[10px] uppercase tracking-[0.2em] text-site-text-faint">
                            Message
                        </label>
                        <textarea
                            rows="7"
                            value={form.data.message}
                            onChange={(event) => form.setData('message', event.target.value)}
                            className="w-full border border-site-border bg-site-card-alt px-4 py-3 text-site-text outline-none transition focus:border-site-amber"
                        />
                        {form.errors.message ? (
                            <p className="mt-2 text-sm text-site-amber">{form.errors.message}</p>
                        ) : null}
                    </div>

                    <button type="submit" disabled={form.processing} className="ghost-button">
                        Send message
                    </button>
                </form>
            </div>
        </main>
    );
}

Contact.layout = (page) => <SiteLayout>{page}</SiteLayout>;

export default Contact;
