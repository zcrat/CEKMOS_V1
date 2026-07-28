import type { option } from '@/types/generales';
import axios from 'axios';

const GetTiposPorCategoria = async (
    categoriaId: number,
): Promise<option[]> => {
    try {
        const response = await axios.get<{ options: option[] }>(
            route('select.tipos'),
            {
                params: { categoria_id: categoriaId },
            },
        );

        return response.data.options;
    } catch (error) {
        console.error(error);

        return [];
    }
};

export default GetTiposPorCategoria;
