import {usePage} from "@inertiajs/react";


export default function Welcome() {

    const props = usePage().props;

    console.log(props)

    return (
        <div>Hola con laravel y react</div>
    )
}
