import { useState } from 'react';
import { Head, useForm, router } from '@inertiajs/react';
import { Page, Card, FormLayout, TextField, Button, DataTable, Badge, Filters, Pagination } from '@shopify/polaris';
import AppLayout from '@/layouts/app-layout';

interface ThumbnailRequest {
    id: number;
    status: string;
    total_images: number;
    processed_images: number;
    created_at: string;
}

interface Props {
    requests: {
        data: ThumbnailRequest[];
        links: any;
        meta: any;
    };
    filters: { status?: string };
    userTier: string;
    quotaLimit: number;
}

export default function Index({ requests, filters, userTier, quotaLimit }: Props) {
    const { data, setData, post, processing, errors } = useForm({
        image_urls: '',
    });

    const [queryValue, setQueryValue] = useState('');

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('thumbnails.store'));
    };

    const handleFiltersChange = (value: string) => {
        router.get(route('thumbnails.index'), { status: value }, { preserveState: true });
    };

    const getStatusBadge = (status: string) => {
        const statusMap = {
            pending: { status: 'info' as const, children: 'Pending' },
            processing: { status: 'attention' as const, children: 'Processing' },
            completed: { status: 'success' as const, children: 'Completed' },
            failed: { status: 'critical' as const, children: 'Failed' },
        };
        return <Badge {...statusMap[status as keyof typeof statusMap]} />;
    };

    const rows = requests.data.map((request) => [
        request.id,
        getStatusBadge(request.status),
        `${request.processed_images}/${request.total_images}`,
        new Date(request.created_at).toLocaleDateString(),
        <Button
            key={request.id}
            variant="plain"
            onClick={() => router.visit(route('thumbnails.show', request.id))}
        >
            View Details
        </Button>,
    ]);

    return (
        <AppLayout>
            <Head title="Thumbnail Processing" />
            
            <Page title="Thumbnail Processing">
                <div style={{ marginBottom: '20px' }}>
                    <Card>
                        <div style={{ padding: '20px' }}>
                            <p><strong>Current Tier:</strong> {userTier.toUpperCase()}</p>
                            <p><strong>Quota Limit:</strong> {quotaLimit} images per request</p>
                        </div>
                    </Card>
                </div>

                <div style={{ display: 'grid', gridTemplateColumns: '400px 1fr', gap: '20px' }}>
                    <div>
                        <Card>
                            <div style={{ padding: '20px' }}>
                                <form onSubmit={handleSubmit}>
                                    <FormLayout>
                                        <div style={{ marginBottom: '8px', fontSize: '14px', color: '#666' }}>
                                            Lines: {data.image_urls.split('\n').filter(line => line.trim()).length} / {quotaLimit}
                                        </div>
                                        <TextField
                                            label="Image URLs (one per line)"
                                            value={data.image_urls}
                                            onChange={(value) => setData('image_urls', value)}
                                            multiline={6}
                                            error={errors.image_urls}
                                            helpText={`Enter up to ${quotaLimit} image URLs, one per line`}
                                        />
                                        <Button submit loading={processing} variant="primary">
                                            Process Images
                                        </Button>
                                    </FormLayout>
                                </form>
                            </div>
                        </Card>
                    </div>

                    <div>
                        <Card>
                            <div style={{ padding: '20px' }}>
                                <div style={{ marginBottom: '16px' }}>
                                    <Filters
                                        queryValue={queryValue}
                                        filters={[
                                            {
                                                key: 'status',
                                                label: 'Status',
                                                filter: (
                                                    <select
                                                        value={filters.status || 'all'}
                                                        onChange={(e) => handleFiltersChange(e.target.value)}
                                                    >
                                                        <option value="all">All</option>
                                                        <option value="pending">Pending</option>
                                                        <option value="processing">Processing</option>
                                                        <option value="completed">Completed</option>
                                                        <option value="failed">Failed</option>
                                                    </select>
                                                ),
                                            },
                                        ]}
                                        onQueryChange={setQueryValue}
                                        onQueryClear={() => setQueryValue('')}
                                    />
                                </div>
                                
                                <DataTable
                                    columnContentTypes={['text', 'text', 'text', 'text', 'text']}
                                    headings={['ID', 'Status', 'Progress', 'Created', 'Actions']}
                                    rows={rows}
                                />
                            </div>
                        </Card>
                    </div>
                </div>
            </Page>
        </AppLayout>
    );
}