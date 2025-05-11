export type Datas = {
    'PhpTsRpc.Tests.Data.Person': {
        name: string;
        age: number;
    };
}

export type Routes = {
    'GET': {
        '/': {
            'request': {
                'person': Datas['PhpTsRpc.Tests.Data.Person']
            },
            'response': {
                'person1': Datas['PhpTsRpc.Tests.Data.Person']
            }
        }
    }
}



declare function call<
    const method extends keyof Routes,
    const path extends keyof Routes[method],
    const params extends Routes[method][path]['request']
>(method: method, path: path, params: params): Promise<Routes[method][path]['response']>


call("GET", "/", { person: { age: 1213, name: '12313' } }).then(e => {
    e.person1.age
    e.person1.name
})