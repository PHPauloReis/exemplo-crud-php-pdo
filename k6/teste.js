import http from 'k6/http';
import { check } from 'k6';

export const options = {
  scenarios: {
    nginx: {
      executor: 'constant-arrival-rate',

      // Tenta manter 1.000 requisições por segundo
      rate: 6000,
      timeUnit: '1s',

      // Durante 30 segundos
      duration: '30s',

      // VUs disponíveis para gerar a carga
      preAllocatedVUs: 600,
      maxVUs: 1500,
    },
  },

  thresholds: {
    // Menos de 1% das requisições podem falhar
    http_req_failed: ['rate<0.01'],

    // 95% das requisições devem responder em menos de 100ms
    http_req_duration: [
        'p(95)<600',
        'p(99)<700'
    ],
  },
};

export default function () {
  const payload = {
    nome: `Produto Teste ${__VU}-${__ITER}`,
    preco: '99.90',
    quantidade: '10',
  };

  const response = http.get('http://localhost:8080/grande.html', payload);

  check(response, {
    'status HTTP é 200': (r) => r.status === 200,
  });
}