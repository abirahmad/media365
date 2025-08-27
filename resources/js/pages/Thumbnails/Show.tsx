import { useState, useEffect } from 'react';
import { Head } from '@inertiajs/react';
import { Page, Card, DataTable, Badge, Button } from '@shopify/polaris';
import AppLayout from '@/layouts/app-layout';
import axios from 'axios';

interface ThumbnailImage {
    id: number;
    original_url: string;
    thumbnail_url: string | null;
    status: string;
    error_message: string | null;
    created_at: string;
}

interface ThumbnailRequest {
    id: number;
    status: string;
    total_images: number;
    processed_images: number;
    created_at: string;
    images: ThumbnailImage[];
}

interface Props {
    request: ThumbnailRequest;
}

export default function Show({ request: initialRequest }: Props) {
    const [request, setRequest] = useState(initialRequest);
    const [isPolling, setIsPolling] = useState(false);

    useEffect(() => {
        const pollStatus = async () => {
            if (isPolling || !request?.id) return;
            setIsPolling(true);
            
            try {
                const response = await axios.get(`/api/thumbnails/${request.id}/status`);
                setRequest(response.data.request);
            } catch (error) {
                console.error('Polling error:', error);
            } finally {
                setIsPolling(false);
            }
        };

        const interval = setInterval(pollStatus, 2000); // Poll every 2 seconds
        return () => clearInterval(interval);
    }, [request?.id]);
    const getStatusBadge = (status: string) => {
        const statusMap = {
            pending: { status: 'info' as const, children: 'Pending' },
            processed: { status: 'success' as const, children: 'Processed' },
            failed: { status: 'critical' as const, children: 'Failed' },
        };
        return <Badge {...statusMap[status as keyof typeof statusMap]} />;
    };

    const rows = request.images.map((image) => [
        image.original_url,
        getStatusBadge(image.status),
        image.thumbnail_url || '-',
        image.error_message || '-',
        new Date(image.created_at).toLocaleDateString(),
    ]);

    return (
        <AppLayout>
            <Head title={`Request #${request.id}`} />
            
            <Page 
                title={`Thumbnail Request #${request.id}`}
                backAction={{ content: 'Back', url: route('thumbnails.index') }}
            >
                <Card>
                    <div style={{ padding: '20px' }}>
                        <div style={{ marginBottom: '20px' }}>
                            <p><strong>Status:</strong> {getStatusBadge(request.status)}</p>
                            <p><strong>Progress:</strong> {request.processed_images}/{request.total_images}</p>
                            <p><strong>Created:</strong> {new Date(request.created_at).toLocaleString()}</p>
                        </div>

                        <DataTable
                            columnContentTypes={['text', 'text', 'text', 'text', 'text']}
                            headings={['Image URL', 'Status', 'Thumbnail URL', 'Error', 'Timestamp']}
                            rows={rows}
                        />
                    </div>
                </Card>
            </Page>
        </AppLayout>
    );
}