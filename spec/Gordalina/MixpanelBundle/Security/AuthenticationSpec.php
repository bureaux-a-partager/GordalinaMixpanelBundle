<?php

namespace spec\Gordalina\MixpanelBundle\Security;

use Gordalina\MixpanelBundle\ManagerRegistry;
use Gordalina\MixpanelBundle\Mixpanel\Flusher;
use Gordalina\MixpanelBundle\Security\UserData;
use PhpSpec\ObjectBehavior;

class AuthenticationSpec extends ObjectBehavior
{
    function let(ManagerRegistry $registry, UserData $userData, Flusher $flusher)
    {
        $this->beConstructedWith($registry, $userData, $flusher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Gordalina\MixpanelBundle\Security\Authentication');
    }

    function it_should_flush_flusher_on_authentication_failure(ManagerRegistry $registry, UserData $userData, Flusher $flusher)
    {
        $project = new \Mixpanel('§');
        $registry->getProjects()->willReturn([$project]);
        $this->onAuthenticationFailure();

        $flusher->flush()->shouldBeCalled();
    }
}
